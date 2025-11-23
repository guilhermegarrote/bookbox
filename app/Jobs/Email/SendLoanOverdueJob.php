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
 * Job responsible for sending an overdue loan notification.
 *
 * This queued job sends an email notifying the user that the loan period has expired.
 * The job is processed asynchronously to prevent delays in the application flow
 * while handling email dispatch.
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
     * The unique identifier of the loan, used to fetch the necessary information.
     */
    protected string $loanId;

    /**
     * Create a new job instance.
     *
     * @param string $loanId The ID of the overdue loan.
     *                       This ID is converted from UUID to binary format for database access.
     */
    public function __construct(string $loanId)
    {
        $this->loanId = $loanId;
    }

    /**
     * Execute the job to send the overdue loan notification email.
     *
     * This method fetches the loan data and sends the overdue notification email
     * using the injected email service.
     *
     * @param EmailService $emailService the service responsible for sending the overdue email
     */
    public function handle(EmailService $emailService): void
    {
        $loan = Loan::findOrFail(Utils::convertUuidToBinary($this->loanId));

        $emailService->sendLoanOverdue($loan);
    }
}
