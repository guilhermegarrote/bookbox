<?php

declare(strict_types=1);

namespace App\Jobs\Email;

use App\Helpers\Utils;
use App\Models\View\Loan;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job responsible for sending a loan receipt email asynchronously.
 *
 * This job sends a confirmation receipt to the borrower after a loan is created.
 * By queuing this task, the system ensures the user's workflow is not delayed,
 * providing a faster response time while the email is processed in the background.
 *
 * @see EmailService::sendLoanReceipt()
 */
class SendLoanReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The unique identifier of the loan, used to fetch the necessary data for the receipt.
     *
     * @var string
     */
    protected string $loanId;

    /**
     * Create a new job instance.
     *
     * @param string $loanId The ID of the loan used to generate the receipt email.
     *                       This ID is a UUID that will be converted to binary format for database queries.
     */
    public function __construct(string $loanId)
    {
        $this->loanId = $loanId;
    }

    /**
     * Execute the job to send the loan receipt email.
     *
     * This method fetches the loan from the database using its binary UUID and
     * passes it to the EmailService for sending the loan receipt email.
     *
     * @param EmailService $emailService The service responsible for sending the loan receipt email.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the loan cannot be found.
     */
    public function handle(EmailService $emailService): void
    {
        $loan = Loan::findOrFail(Utils::convertUuidToBinary($this->loanId));

        $emailService->sendLoanReceipt($loan);
    }
}
