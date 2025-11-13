<?php

declare(strict_types=1);

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Provides standardized and safe error logging with
 * UTF-8 encoding, sensitive data masking, and controlled stack traces.
 */
trait ErrorLoggerTrait
{
    /**
     * Logs an error with consistent formatting and context.
     *
     * @param string $message descriptive error message
     * @param \Throwable $exception the thrown exception
     * @param array<string,mixed> $context additional context to include in the log
     * @param null|string $channel Optional log channel (e.g., 'classes').
     */
    protected function logError(string $message, \Throwable $exception, array $context = [], ?string $channel = null): void
    {
        $context = $this->sanitizeSensitiveData($context);

        $context = $this->encodeStringsUtf8($context);

        $logData = array_merge($context, [
            'exception_class' => \get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'trace' => config('app.debug') ? $this->getLimitedTrace($exception, 12) : null,
        ]);

        $logger = Log::channel($channel ?? 'errors');
        $logger->error($message, $logData);
    }

    /**
     * Masks common sensitive fields (e.g., passwords, tokens) in the given context array.
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>
     */
    protected function sanitizeSensitiveData(array $data): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];

        foreach ($sensitiveKeys as $key) {
            if (\array_key_exists($key, $data)) {
                $data[$key] = '*****';
            }
        }

        return $data;
    }

    /**
     * Recursively converts all string values to UTF-8.
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>
     */
    protected function encodeStringsUtf8(array $data): array
    {
        array_walk_recursive($data, function (&$value): void {
            if (\is_string($value)) {
                $encoding = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'ASCII'], true);
                $value = mb_convert_encoding($value, 'UTF-8', $encoding ?: 'UTF-8');
            }
        });

        return $data;
    }

    /**
     * Returns a string representation of the exception trace, limited to the given number of lines.
     */
    protected function getLimitedTrace(\Throwable $exception, int $maxLines = 10): string
    {
        $traceLines = explode("\n", $exception->getTraceAsString());

        return implode("\n", \array_slice($traceLines, 0, $maxLines));
    }
}
