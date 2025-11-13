<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\View\Loan as ViewLoan;
use App\Services\ThermalPrinterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to print the loan receipt asynchronously.
 *
 * This job decouples the printing process from the main controller flow
 * to avoid slowing down API responses.
 */
class PrintLoanReceiptJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var string UUID of the loan to print
     */
    private string $loanId;

    /**
     * @var string Name of the user performing the loan
     */
    private string $userName;

    /**
     * Create a new job instance.
     *
     * @param string $loanId UUID of the loan
     * @param string $userName Name of the user performing the loan
     */
    public function __construct(string $loanId, string $userName)
    {
        $this->loanId = $loanId;
        $this->userName = $userName;
    }

    /**
     * Execute the job.
     *
     * Fetches the loan data and prints a receipt using the thermal printer service.
     */
    public function handle(): void
    {
        $loan = ViewLoan::find($this->loanId);

        if (!$loan) {
            Log::warning("Loan not found for printing receipt: {$this->loanId}");

            return;
        }

        try {
            $printer = new ThermalPrinterService();
            $printer->printLoanReceipt($loan, $this->userName);
        } catch (\Throwable $e) {
            Log::error("Failed to print loan receipt for loan {$this->loanId}", [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
