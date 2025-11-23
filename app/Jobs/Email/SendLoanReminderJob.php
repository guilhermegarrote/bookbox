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

/**
 * Job responsible for sending a loan reminder email.
 *
 * This queued job sends an email to remind the user that the loan due date is approaching.
 * By delegating this task to the EmailService, we ensure asynchronous processing, avoiding
 * delays in the HTTP request and improving user experience.
 *
 * @see EmailService::sendLoanReminder()
 */
class SendLoanReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The unique identifier of the loan, used to fetch the necessary data.
     */
    protected string $loanId;

    /**
     * Create a new job instance.
     *
     * @param string $loanId The ID of the loan used to generate the reminder email.
     *                       The ID is converted from UUID to binary for database access.
     */
    public function __construct(string $loanId)
    {
        $this->loanId = $loanId;
    }

    /**
     * Execute the job to send the loan reminder email.
     *
     * This method retrieves the loan data based on the provided loan ID and triggers
     * the email service to send the reminder to the user.
     *
     * @param EmailService $emailService The service responsible for sending the reminder email.
     */
    public function handle(EmailService $emailService): void
    {
        $loan = Loan::findOrFail(Utils::convertUuidToBinary($this->loanId));

        $emailService->sendLoanReminder($loan);
    }
}
