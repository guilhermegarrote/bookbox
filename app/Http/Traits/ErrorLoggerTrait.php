<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

trait ErrorLoggerTrait
{
    /**
     * Standardized error logger with context, encoding and controlled trace.
     *
     * @param string $message Descriptive error message
     * @param \Throwable $exception The thrown exception
     * @param array $extraContext Additional data to include in the log
     * @param string|null $channel Optional log channel (e.g. 'classes')
     * @return void
     */
    protected function logError(string $message, Throwable $exception, array $extraContext = [], ?string $channel = null): void
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token'];
        foreach ($sensitiveFields as $field) {
            if (isset($extraContext[$field])) {
                $extraContext[$field] = '*****';
            }
        }

        $extraContext = $this->encodeStringsUtf8($extraContext);

        $logData = array_merge($extraContext, [
            'exception_class' => get_class($exception),
            'error' => $exception->getMessage(),
            'trace' => config('app.debug') ? $this->getLimitedTrace($exception, 10) : null,
        ]);

        if ($channel) {
            Log::channel($channel)->error($message, $logData);
        } else {
            Log::error($message, $logData);
        }
    }

    /**
     * Recursively converts all strings in the array to UTF-8.
     */
    protected function encodeStringsUtf8(array $data): array
    {
        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        });

        return $data;
    }

    /**
     * Returns the exception trace limited to a maximum number of lines.
     */
    protected function getLimitedTrace(Throwable $exception, int $maxLines = 10): string
    {
        $traceLines = explode("\n", $exception->getTraceAsString());
        $limited = array_slice($traceLines, 0, $maxLines);
        return implode("\n", $limited);
    }
}
