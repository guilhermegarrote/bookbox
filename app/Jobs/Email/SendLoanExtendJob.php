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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The unique identifier of the loan, used to fetch the necessary data.
     */
    protected string $loanId;

    /**
     * Create a new job instance.
     *
     * @param string $loanId The loan whose due date was extended.
     *                       This ID is converted from UUID to binary for database access.
     */
    public function __construct(string $loanId)
    {
        $this->loanId = $loanId;
    }

    /**
     * Execute the job to send the due date extension email.
     *
     * This method retrieves the loan based on the provided ID and requests
     * the email service to send the extension email with the relevant details.
     *
     * @param EmailService $emailService Service responsible for composing
     *                                   and dispatching the extension email.
     */
    public function handle(EmailService $emailService): void
    {
        $loan = Loan::findOrFail(Utils::convertUuidToBinary($this->loanId));

        $emailService->sendLoanExtend($loan);
    }
}
