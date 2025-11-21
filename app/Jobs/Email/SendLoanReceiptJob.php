<?php

declare(strict_types=1);

namespace App\Jobs\Email;

use App\Models\View\Loan;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job responsible for sending a loan receipt email.
 *
 * This job asynchronously sends a receipt confirming that the loan was successfully created.
 * Delegating this process to a queued job avoids delay in the user's workflow.
 *
 * @see EmailService::sendLoanReceipt()
 */
class SendLoanReceiptJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Loan instance containing the data needed for the receipt email.
     */
    protected Loan $loan;

    /**
     * Create a new job instance.
     *
     * @param Loan $loan The loan to be used for generating the receipt email
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Execute the job.
     *
     * @param EmailService $emailService Injected email service that handles sending the receipt email
     */
    public function handle(EmailService $emailService): void
    {
        $emailService->sendLoanReceipt($this->loan);
    }
}
