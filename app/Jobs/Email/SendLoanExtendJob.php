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
 * Job responsible for sending a loan due date extension email.
 *
 * This job asynchronously notifies the borrower that the due date of an
 * existing loan has been successfully extended. Queueing the email ensures
 * that the user experience remains fast and avoids delays caused by
 * external email dispatching.
 *
 * @see EmailService::sendLoanExtend()
 */
class SendLoanExtendJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Loan instance containing the data required to generate
     * the due date extension email.
     */
    protected Loan $loan;

    /**
     * Create a new job instance.
     *
     * @param Loan $loan The loan whose due date was extended.
     *                   Includes borrower, book and renewal details.
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Execute the job.
     *
     * @param EmailService $emailService service responsible for composing
     *                                   and dispatching the extension email
     */
    public function handle(EmailService $emailService): void
    {
        $emailService->sendLoanExtend($this->loan);
    }
}
