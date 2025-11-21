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
 * Job responsible for sending an overdue loan notification.
 *
 * This queued job sends an email informing the user that the loan period has expired.
 * The process runs asynchronously to avoid slowing down the application flow.
 *
 * @see EmailService::sendLoanOverdue()
 */
class SendLoanOverdueJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Loan instance containing user and overdue information.
     */
    protected Loan $loan;

    /**
     * Create a new job instance.
     *
     * @param Loan $loan The overdue loan instance
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Execute the job.
     *
     * @param EmailService $emailService Injected email service responsible for sending the overdue notification
     */
    public function handle(EmailService $emailService): void
    {
        $emailService->sendLoanOverdue($this->loan);
    }
}
