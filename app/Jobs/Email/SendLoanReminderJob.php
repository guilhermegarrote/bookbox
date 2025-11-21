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
 * Job responsible for sending a loan reminder email.
 *
 * This queued job sends a reminder notifying the user that the loan due date is approaching.
 * The job delegates the task to the EmailService, ensuring asynchronous processing and
 * preventing delays in HTTP requests.
 *
 * @see EmailService::sendLoanReminder()
 */
class SendLoanReminderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The loan instance containing user and book data.
     */
    protected Loan $loan;

    /**
     * Create a new job instance.
     *
     * @param Loan $loan The loan information used to generate the reminder email
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Execute the job.
     *
     * @param EmailService $emailService Injected email service responsible for sending the reminder
     */
    public function handle(EmailService $emailService): void
    {
        $emailService->sendLoanReminder($this->loan);
    }
}
