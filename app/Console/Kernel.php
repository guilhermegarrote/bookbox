<?php

declare(strict_types=1);

namespace App\Console;

use App\Jobs\Email\SendLoanOverdueJob;
use App\Jobs\Email\SendLoanReminderJob;
use App\Models\View\Loan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Manages the application's scheduled commands and custom scheduling logic.
 * This class is responsible for ensuring loan reminder and overdue notifications are sent on time.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * This method schedules tasks such as:
     * - Sending loan reminders for loans due in 1 day and 7 days.
     * - Sending overdue notifications for loans that are overdue, based on the number of overdue days.
     *
     * @param Schedule $schedule the schedule instance used for defining command schedules
     */
    protected function schedule(Schedule $schedule): void
    {
        /**
         * Loan Reminder Notifications:
         * - Sends reminders for loans due in 1 day and 7 days.
         *
         * The reminder is sent for each loan that has not been returned and whose
         * due date is within these target periods.
         */
        $schedule->call(function (): void {
            $today = now()->startOfDay();

            $targetDates = [
                $today->copy()->addDay()->toDateString(),   // tomorrow
                $today->copy()->addDays(7)->toDateString(), // in 7 days
            ];

            $loans = Loan::whereNull('loan_returned_date')
                ->whereDate('loan_due_date', $targetDates)
                ->get();

            foreach ($loans as $loan) {
                SendLoanReminderJob::dispatch($loan->id);
            }
        })->dailyAt('03:00');

        /**
         * Loan Overdue Notifications:
         * - Sends a notification 1 day after the due date.
         * - Sends additional notifications every 3 days after the first overdue day.
         *
         * This logic determines when to send overdue notifications based on the number of days overdue.
         */
        $schedule->call(function (): void {
            $loans = Loan::whereNull('loan_returned_date')
                ->where('loan_due_date', '<', now()->startOfDay())
                ->get();

            foreach ($loans as $loan) {
                $daysOverdue = now()->startOfDay()->diffInDays($loan->loan_due_date);

                if ($daysOverdue === 1 || ($daysOverdue > 1 && ($daysOverdue - 1) % 3 === 0)) {
                    SendLoanOverdueJob::dispatch($loan->id);
                }
            }
        })->dailyAt('03:15');

        $schedule->command('labels:cleanup')->daily();
        $schedule->command('optimize:clear')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * This method loads the application's custom console commands from the `Commands` directory
     * and includes the route definitions from `routes/console.php`.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
