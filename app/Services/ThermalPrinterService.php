<?php

namespace App\Services;

use App\Models\View\Loan;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ThermalPrinterService
{
    protected Printer $printer;

    /**
     * Constructor
     * Initializes the printer connection using optional network or local settings.
     *
     * @param string|null $connection IP address or printer name
     * @param int|null $port Network port for printer
     */
    public function __construct(?string $connection = null, ?int $port = null)
    {
        $this->printer = $this->createPrinter($connection, $port);
    }

    /**
     * Create a printer instance with a specific capability profile.
     *
     * @param string|null $connection IP address or printer name
     * @param int|null $port Network port for printer
     * @param string $profileName Capability profile name
     * @return Printer
     */
    private function createPrinter(?string $connection = null, ?int $port = null, string $profileName = 'default'): Printer
    {
        $connector = $this->createConnector($connection, $port);
        $profile = CapabilityProfile::load($profileName);
        return new Printer($connector, $profile);
    }

    /**
     * Create the appropriate printer connector.
     * Uses network connector if IP address is provided, otherwise uses Windows connector.
     *
     * @param string|null $connection IP address or printer name
     * @param int|null $port Network port for printer
     * @return NetworkPrintConnector|WindowsPrintConnector
     */
    private function createConnector(?string $connection, ?int $port = null)
    {
        if ($connection && filter_var($connection, FILTER_VALIDATE_IP)) {
            return new NetworkPrintConnector($connection, $port ?? 9100);
        }

        return new WindowsPrintConnector($connection ?? "ELGIN i8");
    }

    /**
     * Print the loan receipt.
     * Handles both single copy and a second copy for the school if needed.
     *
     * @param Loan|array $loan Loan model or array with loan data
     * @param string $employeeName Name of the employee handling the loan
     */
    public function printLoanReceipt(Loan|array $loan, string $employeeName = 'Desconhecido'): void
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
        } catch (\Exception $e) {
            Log::error("Error printing second copy: " . $e->getMessage());
        } finally {
            $printer->close();
        }
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
        } catch (\Exception $e) {
            Log::error("Error printing first copy: " . $e->getMessage());
        } finally {
            $this->printer->close();
        }
    }

    /**
     * Print the printer header including the logo.
     *
     * @param Printer $printer The printer instance
     */
    private function printHeader(Printer $printer): void
    {
        $logoPath = public_path('images/logo/logotype_print.png');

        if (!file_exists($logoPath) || !is_readable($logoPath)) {
            Log::warning("Logo not found or unreadable: $logoPath");
            return;
        }

        try {
            $image = EscposImage::load($logoPath, false);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->bitImage($image);
            $printer->feed(2);
        } catch (\Exception $e) {
            Log::warning("Failed to load logo: " . $e->getMessage());
        }
    }

    /**
     * Print the loan receipt content.
     * Optionally marks it as a school copy with signature line.
     *
     * @param Printer $printer Printer instance
     * @param array $loan Loan data
     * @param bool $isSchoolCopy Whether this is a copy for school records
     */
    private function printCopy(Printer $printer, array $loan, bool $isSchoolCopy = false): void
    {
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text("RECIBO DE EMPRÉSTIMO\n");
        $printer->setEmphasis(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $fields = [
            'Código' => $loan['barcode_code'] ?? '',
            'Responsável' => ($loan['employee_name'] ?? '') . "\n",
            'Aluno' => $loan['name'] ?? '',
            'CPF' => $loan['cpf'] ?? '',
            'Turma' => $loan['formatted_class'] . "\n" ?? '',
            'ISBN' => $loan['isbn'] ?? '',
            'Título do livro' => $loan['title'] ?? '',
            'Exemplar' => $loan['number'] ?? '',
            'Autor' => $loan['author'] . "\n" ?? '',
            'Data do empréstimo' => $loan['loan_start_date'] ?? '',
        ];

        foreach ($fields as $label => $value) {
            $printer->text("$label: $value\n");
        }

        $printer->feed(1);

        $dueDate = $loan['loan_due_date'] ?? '';
        $lineWidth = 48;
        $title = "Data de devolução";

        $centerText = function (string $text, int $width): string {
            $padding = max(0, intval(($width - mb_strlen($text)) / 2));
            return str_repeat(" ", $padding) . $text . str_repeat(" ", $width - mb_strlen($text) - $padding);
        };

        $top = "╔" . str_repeat("═", $lineWidth - 2) . "╗\n";
        $middle1 = "║" . $centerText($title, $lineWidth - 2) . "║\n";
        $middle2 = "║" . $centerText($dueDate, $lineWidth - 2) . "║\n";
        $bottom = "╚" . str_repeat("═", $lineWidth - 2) . "╝\n";

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text($top . $middle1 . $middle2 . $bottom);
        $printer->setEmphasis(false);

        $printer->feed(1);

        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode(
                $loan['barcode_code'] ?? '',
                $generator::TYPE_CODE_128,
                2,
                80
            );

            $tmpFile = tempnam(sys_get_temp_dir(), 'barcode') . '.png';
            file_put_contents($tmpFile, $barcodeData);
            $image = EscposImage::load($tmpFile);
            $printer->bitImage($image);
            unlink($tmpFile);
        } catch (\Exception $e) {
            Log::warning("Failed to print barcode: " . $e->getMessage());
        }

        $printer->feed(1);

        if ($isSchoolCopy) {
            $printer->feed(1);
            $printer->text("Assinatura do aluno:\n\n");
            $printer->text(str_repeat("_", 38) . "\n");
        }
    }
}
