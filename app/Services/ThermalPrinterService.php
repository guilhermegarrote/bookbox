<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\View\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * Class ThermalPrinterService.
 *
 * Responsible for building loan receipt payloads and sending them to a Node.js printing service.
 * All visible text in the receipt is in Portuguese.
 */
class ThermalPrinterService
{
    /**
     * Default printer service host (e.g., local Node.js client service).
     */
    private string $defaultClientHost;

    public function __construct()
    {
        $this->defaultClientHost = config('services.client_printer.host', env('CLIENT_PRINTER_HOST', 'http://localhost:3000'));
    }

    /**
     * Main method to generate and send a loan receipt to the client printer.
     *
     * @param array|Loan $loan Loan data array or Loan model instance
     * @param string $employeeName Name of the employee processing the loan
     */
    public function printLoanReceipt(array|Loan $loan, string $employeeName = 'Desconhecido'): void
    {
        if ($loan instanceof Loan) {
            $loan = $loan->toArray();
        }

        $loan['employee_name'] = $employeeName;

        try {
            $payload = $this->buildPrintPayload($loan);
            $this->sendToClientPrinter($payload, $loan['client_host'] ?? null);

            Log::info('Loan receipt sent to the print service.', [
                'aluno' => $loan['name'] ?? null,
                'livro' => $loan['title'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error preparing or sending loan receipt to print: ' . $e->getMessage(), [
                'loan_id' => $loan['id'] ?? null,
                'exception' => $e,
            ]);
        }
    }

    /**
     * Build the text and image payload for the receipt.
     *
     * @param array $loan Loan data
     *
     * @return array Receipt payload including text lines, logo, barcode, and options
     */
    private function buildPrintPayload(array $loan): array
    {
        $lines = [];

        $lines[] = ['align' => 'center', 'emphasis' => true, 'text' => 'RECIBO DE EMPRÉSTIMO'];
        $lines[] = ['align' => 'center', 'text' => ''];

        $fields = [
            'Código' => $loan['barcode_code'] ?? '',
            'Responsável' => $loan['employee_name'] ?? '',
            'Aluno' => $loan['name'] ?? '',
            'CPF' => $loan['cpf'] ?? '',
            'Turma' => $loan['formatted_class'] ?? '',
            'ISBN' => $loan['isbn'] ?? '',
            'Título do livro' => $loan['title'] ?? '',
            'Exemplar' => $loan['number'] ?? '',
            'Autor' => $loan['author'] ?? '',
            'Data do empréstimo' => Carbon::parse($loan['loan_start_date'])->format('d/m/Y')
                ?? '',
        ];

        foreach ($fields as $label => $value) {
            $lines[] = ['align' => 'left', 'text' => "{$label}: {$value}"];
        }

        $lines[] = ['text' => ''];

        $dueDate = Carbon::parse($loan['loan_due_date'])->format('d/m/Y') ?? '';
        $box = $this->buildDueDateBoxText($dueDate, 48);

        foreach ($box as $line) {
            $lines[] = ['align' => 'center', 'emphasis' => true, 'text' => $line];
        }

        $lines[] = ['text' => ''];

        $options = [
            'school_copy' => true,
            'school_copy_after_ms' => 500,
        ];

        return [
            'text_lines' => $lines,
            'logo_base64' => $this->loadLogoBase64(),
            'barcode_base64' => $this->buildBarcodeBase64($loan['barcode_code'] ?? ''),
            'copies' => 1,
            'options' => $options,
            'summary' => [
                'aluno' => $loan['name'] ?? null,
                'livro' => $loan['title'] ?? null,
                'codigo' => $loan['barcode_code'] ?? null,
            ],
        ];
    }

    /**
     * Build a visual box around the due date using box-drawing characters.
     *
     * @param string $dueDate The loan due date
     * @param int $lineWidth Width of the box in characters
     *
     * @return array Lines representing the box with centered text
     */
    private function buildDueDateBoxText(string $dueDate, int $lineWidth = 48): array
    {
        $title = 'Data de devolução';

        $centerText = function (string $text, int $width): string {
            $len = mb_strlen($text);
            $pad = (int) floor(max(0, ($width - $len) / 2));

            return str_repeat(' ', $pad) . $text . str_repeat(' ', max(0, $width - $len - $pad));
        };

        return [
            '╔' . str_repeat('═', $lineWidth - 2) . '╗',
            '║' . $centerText($title, $lineWidth - 2) . '║',
            '║' . $centerText($dueDate, $lineWidth - 2) . '║',
            '╚' . str_repeat('═', $lineWidth - 2) . '╝',
        ];
    }

    /**
     * Load the school's logo as a Base64-encoded PNG image.
     *
     * @return null|string Base64 string or null if logo not found
     */
    private function loadLogoBase64(): ?string
    {
        $logoPath = public_path('images/logo/logotype_print.png');

        if (!file_exists($logoPath)) {
            Log::warning("Logo not found at {$logoPath}");

            return null;
        }

        try {
            return base64_encode(file_get_contents($logoPath));
        } catch (\Throwable $e) {
            Log::warning('Failed to load logo: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Generate a Base64-encoded barcode image for the given code.
     *
     * @param string $code Code to encode as barcode
     *
     * @return null|string Base64 barcode image or null if empty or failed
     */
    private function buildBarcodeBase64(string $code): ?string
    {
        if (trim($code) === '') {
            return null;
        }

        try {
            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 80);

            return base64_encode($barcode);
        } catch (\Throwable $e) {
            Log::warning('Failed to generate barcode: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Sends the receipt payload to the Node.js client printing service.
     *
     * @param array $payload The prepared receipt payload
     * @param null|string $clientHost Optional override for the client printer host
     */
    private function sendToClientPrinter(array $payload, ?string $clientHost = null): void
    {
        $host = $clientHost ? rtrim($clientHost, '/') : $this->defaultClientHost;
        $url = "{$host}/print";

        try {
            $response = Http::timeout(10)->post($url, $payload);

            if (!$response->successful()) {
                Log::error("Failed to send payload to print service ({$response->status()}): " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Error communicating with the print service: ' . $e->getMessage(), [
                'url' => $url,
            ]);
        }
    }
}
