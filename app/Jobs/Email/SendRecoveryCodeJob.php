<?php

declare(strict_types=1);

namespace App\Jobs\Email;

use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job responsible for asynchronously sending a recovery code e-mail.
 *
 * This job delegates the actual sending logic to the EmailService and ensures
 * that the operation is executed through Laravel's queue system.
 */
class SendRecoveryCodeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Recipient e-mail address.
     */
    protected string $to;

    /**
     * Recipient full name.
     */
    protected string $name;

    /**
     * Recovery code to be sent.
     */
    protected string $code;

    /**
     * Create a new job instance.
     *
     * @param string $to recipient e-mail address
     * @param string $name recipient full name
     * @param string $code recovery code to be delivered
     */
    public function __construct(string $to, string $name, string $code)
    {
        $this->to = $to;
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @param EmailService $emailService service responsible for sending emails
     */
    public function handle(EmailService $emailService): void
    {
        $emailService->sendRecoveryCode($this->to, $this->name, $this->code);
    }
}
