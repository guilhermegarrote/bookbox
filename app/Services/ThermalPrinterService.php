<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\View\Loan;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * Class ThermalPrinterService.
 *
 * Handles printing of loan receipts using thermal printers.
 * Supports both network and local printer connections, prints logos,
 * barcodes, and optionally a school copy with signature line.
 */
class ThermalPrinterService
{
    /**
     * @var Printer The active printer instance
     */
    protected Printer $printer;

    /**
     * ThermalPrinterService constructor.
     *
     * Initializes a printer connection using either network or local settings.
     *
     * @param null|string $connection Optional IP address or local printer name
     * @param null|int $port Optional network port (default 9100)
     */
    public function __construct(?string $connection = null, ?int $port = null)
    {
        $this->printer = $this->createPrinter($connection, $port);
    }

    /**
     * Print a loan receipt.
     *
     * Handles printing a copy for the borrower and an optional school copy.
     *
     * @param array|Loan $loan Loan model or array containing loan data
     * @param string $employeeName Employee name handling the loan (default 'Desconhecido')
     */
    public function printLoanReceipt(array|Loan $loan, string $employeeName = 'Desconhecido'): void
    {
        if ($loan instanceof Loan) {
            $loan = $loan->toArray();
        }

        $loan['employee_name'] = $employeeName;

        $this->printSingleCopy($loan);

        usleep(500000);

        $printer = $this->createPrinter();

        try {
            $this->printHeader($printer);
            $this->printCopy($printer, $loan, true);
            $printer->feed(2);
            $printer->cut();
        } catch (\Throwable $e) {
            Log::error('Erro imprimindo cópia escolar: ' . $e->getMessage());
        } finally {
            $printer->close();
        }
    }

    /**
     * Create a configured printer instance.
     *
     * @param null|string $connection IP address or printer name
     * @param null|int $port Network port
     * @param string $profileName Capability profile name (default 'default')
     *
     * @return Printer Configured printer object
     */
    private function createPrinter(?string $connection = null, ?int $port = null, string $profileName = 'default'): Printer
    {
        $connector = $this->createConnector($connection, $port);
        $profile = CapabilityProfile::load($profileName);

        return new Printer($connector, $profile);
    }

    /**
     * Create printer connector.
     *
     * Uses network connector if IP provided; otherwise falls back to Windows connector.
     *
     * @param null|string $connection IP address or printer name
     * @param null|int $port Network port
     *
     * @return NetworkPrintConnector|WindowsPrintConnector
     */
    private function createConnector(?string $connection, ?int $port = null)
    {
        if ($connection && filter_var($connection, FILTER_VALIDATE_IP)) {
            return new NetworkPrintConnector($connection, $port ?? 9100);
        }

        return new WindowsPrintConnector($connection ?? 'ELGIN i8');
    }

    /**
     * Print a single copy of the loan receipt.
     *
     * @param array $loan Loan data
     */
    private function printSingleCopy(array $loan): void
    {
        try {
            $this->printHeader($this->printer);
            $this->printCopy($this->printer, $loan);
            $this->printer->feed(2);
            $this->printer->cut();
        } catch (\Throwable $e) {
            Log::error('Erro imprimindo primeira cópia: ' . $e->getMessage());
        } finally {
            $this->printer->close();
        }
    }

    /**
     * Print printer header, including logo if exists.
     *
     * @param Printer $printer Printer instance
     */
    private function printHeader(Printer $printer): void
    {
        $logoPath = public_path('images/logo/logotype_print.png');

        if (!file_exists($logoPath) || !is_readable($logoPath)) {
            Log::warning("Logo não encontrada ou não legível: {$logoPath}");

            return;
        }

        try {
            $image = EscposImage::load($logoPath, false);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->bitImage($image);
            $printer->feed(2);
        } catch (\Throwable $e) {
            Log::warning('Falha ao carregar logo: ' . $e->getMessage());
        }
    }

    /**
     * Print loan receipt content.
     *
     * @param Printer $printer Printer instance
     * @param array $loan Loan data
     * @param bool $isSchoolCopy Optional flag to include school copy signature line
     */
    private function printCopy(Printer $printer, array $loan, bool $isSchoolCopy = false): void
    {
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text("LOAN RECEIPT\n");
        $printer->setEmphasis(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $fields = [
            'Code' => $loan['barcode_code'] ?? '',
            'Employee' => $loan['employee_name'] ?? '',
            'Student' => $loan['name'] ?? '',
            'CPF' => $loan['cpf'] ?? '',
            'Class' => $loan['formatted_class'] ?? '',
            'ISBN' => $loan['isbn'] ?? '',
            'Book title' => $loan['title'] ?? '',
            'Copy number' => $loan['number'] ?? '',
            'Author' => $loan['author'] ?? '',
            'Loan date' => $loan['loan_start_date'] ?? '',
        ];

        foreach ($fields as $label => $value) {
            $printer->text("{$label}: {$value}\n");
        }

        $printer->feed(1);

        $this->printDueDateBox($printer, $loan['loan_due_date'] ?? '');

        $this->printBarcode($printer, $loan['barcode_code'] ?? '');

        if ($isSchoolCopy) {
            $printer->feed(1);
            $printer->text("Student signature:\n\n");
            $printer->text(str_repeat('_', 38) . "\n");
        }
    }

    /**
     * Print due date inside a box.
     */
    private function printDueDateBox(Printer $printer, string $dueDate): void
    {
        $lineWidth = 48;
        $title = 'Due Date';

        $centerText = function (string $text, int $width): string {
            $padding = max(0, \intval(($width - mb_strlen($text)) / 2));

            return str_repeat(' ', $padding) . $text . str_repeat(' ', $width - mb_strlen($text) - $padding);
        };

        $top = '╔' . str_repeat('═', $lineWidth - 2) . "╗\n";
        $middle1 = '║' . $centerText($title, $lineWidth - 2) . "║\n";
        $middle2 = '║' . $centerText($dueDate, $lineWidth - 2) . "║\n";
        $bottom = '╚' . str_repeat('═', $lineWidth - 2) . "╝\n";

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text($top . $middle1 . $middle2 . $bottom);
        $printer->setEmphasis(false);
        $printer->feed(1);
    }

    /**
     * Print a barcode for the loan.
     */
    private function printBarcode(Printer $printer, string $barcode): void
    {
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 80);

            $tmpFile = tempnam(sys_get_temp_dir(), 'barcode') . '.png';
            file_put_contents($tmpFile, $barcodeData);

            $image = EscposImage::load($tmpFile);
            $printer->bitImage($image);

            unlink($tmpFile);
        } catch (\Throwable $e) {
            Log::warning('Erro ao imprimir código de barra: ' . $e->getMessage());
        }

        $printer->feed(1);
    }
}
