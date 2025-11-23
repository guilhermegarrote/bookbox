<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\View\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Service responsible for composing and sending application emails using PHPMailer.
 *
 * This service centralizes email-related operations, including:
 * - Sending recovery codes
 * - Loan reminders
 * - Overdue loan notifications
 * - Loan receipts
 * - Loan extensions
 */
class EmailService
{
    /**
     * PHPMailer instance configured for SMTP.
     *
     * @var PHPMailer
     */
    protected PHPMailer $mailer;

    /**
     * EmailService constructor.
     *
     * @param PHPMailer $mailer Fully configured PHPMailer instance.
     */
    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Sends a 6-digit recovery code to a user.
     *
     * @param string $to Recipient email address.
     * @param string $name Recipient full name.
     * @param string $code Recovery code.
     *
     * @return void
     * @throws Exception If sending the email fails.
     */
    public function sendRecoveryCode(string $to, string $name, string $code): void
    {
        $subject = 'Código de Recuperação';
        $htmlBody = View::make('emails.auth.recovery-code', compact('name', 'code'))->render();
        $altBody  = "Olá {$name}, seu código de recuperação é: {$code}.";
        $this->sendEmail($to, $name, $subject, $htmlBody, $altBody);
    }

    /**
     * Sends a reminder email for a loan approaching its due date.
     *
     * @param Loan $loan Loan instance.
     *
     * @return void
     * @throws Exception
     */
    public function sendLoanReminder(Loan $loan): void
    {
        $data = $this->prepareLoanData($loan);
        $dueDate = Carbon::parse($loan->loan_due_date);
        $data['loan_remaining_days'] = max(0, $dueDate->diffInDays(Carbon::now()));

        $subject = 'Lembrete de Empréstimo';
        $htmlBody = View::make('emails.loans.reminder', $data)->render();
        $altBody = "Olá {$data['student_name']}, seu empréstimo vence em {$data['loan_remaining_days']} dia" . ($data['loan_remaining_days'] > 1 ? 's' : '') . ".";

        $this->sendEmail($data['email'], $data['student_name'], $subject, $htmlBody, $altBody);
    }

    /**
     * Sends a notification email for overdue loans.
     *
     * @param Loan $loan Loan instance.
     *
     * @return void
     * @throws Exception
     */
    public function sendLoanOverdue(Loan $loan): void
    {
        $data = $this->prepareLoanData($loan);
        $dueDate = Carbon::parse($loan->loan_due_date);
        $data['loan_days_late'] = max(0, Carbon::now()->diffInDays($dueDate, false) * -1);

        $subject = 'Notificação de Empréstimo Vencido';
        $htmlBody = View::make('emails.loans.overdue', $data)->render();
        $altBody = "Olá {$data['student_name']}, seu empréstimo está vencido há {$data['loan_days_late']} dia" . ($data['loan_days_late'] > 1 ? 's' : '') . ".";

        $this->sendEmail($data['email'], $data['student_name'], $subject, $htmlBody, $altBody);
    }

    /**
     * Sends a loan receipt email immediately after loan creation.
     *
     * @param Loan $loan Loan instance.
     *
     * @return void
     * @throws Exception
     */
    public function sendLoanReceipt(Loan $loan): void
    {
        $data = $this->prepareLoanData($loan);

        $subject = 'Comprovante de Empréstimo';
        $htmlBody = View::make('emails.loans.receipt', $data)->render();
        $altBody = "Olá {$data['student_name']},\n\nEste é o seu comprovante de empréstimo para '{$data['book_title']}'.\n"
            . "Autor: {$data['book_author']}\nNúmero de cópia: {$data['book_copy_number']}\n"
            . "Data de início: {$data['loan_start_date']}\nData de devolução: {$data['loan_due_date']}.";

        $this->sendEmail($data['email'], $data['student_name'], $subject, $htmlBody, $altBody);
    }

    /**
     * Sends an email notifying the borrower of a loan due-date extension.
     *
     * @param Loan $loan Loan instance.
     *
     * @return void
     * @throws Exception
     */
    public function sendLoanExtend(Loan $loan): void
    {
        $data = $this->prepareLoanData($loan);
        $oldDue = Carbon::parse($loan->loan_due_date);
        $newDue = $oldDue->copy()->addDays(7);

        $data['loan_old_due_date'] = $oldDue->format('d/m/Y');
        $data['loan_new_due_date'] = $newDue->format('d/m/Y');

        $subject = 'Extensão de Empréstimo';
        $htmlBody = View::make('emails.loans.extend', $data)->render();
        $altBody = "Olá {$data['student_name']},\n\nA data de devolução do seu empréstimo foi estendida.\n"
            . "ISBN: {$data['book_isbn']}\nTítulo: {$data['book_title']}\nAutor: {$data['book_author']}\nNúmero de cópia: {$data['book_copy_number']}\n"
            . "Data de início: {$data['loan_start_date']}\nData original: {$data['loan_old_due_date']}\nNova data: {$data['loan_new_due_date']}.";

        $this->sendEmail($data['email'], $data['student_name'], $subject, $htmlBody, $altBody);
    }

    /**
     * Prepares loan data array for email templates.
     *
     * @param Loan $loan Loan instance.
     *
     * @return array<string,mixed> Associative array of loan details.
     */
    protected function prepareLoanData(Loan $loan): array
    {
        return [
            'email' => $loan->email,
            'student_name' => $loan->name,
            'student_school_class' => $loan->formatted_class_name,
            'book_isbn' => $loan->isbn,
            'book_title' => $loan->title,
            'book_author' => $loan->author,
            'book_copy_number' => $loan->number,
            'loan_start_date' => Carbon::parse($loan->loan_start_date)->format('d/m/Y'),
            'loan_due_date' => Carbon::parse($loan->loan_due_date)->format('d/m/Y'),
        ];
    }

    /**
     * Sends an email using PHPMailer.
     *
     * @param string $to Recipient email address.
     * @param string $name Recipient full name.
     * @param string $subject Email subject.
     * @param string $htmlBody HTML content of the email.
     * @param string $altBody Plain-text alternative content.
     *
     * @return void
     * @throws Exception If sending the email fails.
     */
    protected function sendEmail(string $to, string $name, string $subject, string $htmlBody, string $altBody): void
    {
        try {
            if (empty($htmlBody)) {
                throw new Exception('The HTML email body cannot be empty.');
            }

            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
            $this->mailer->addAddress($to, $name);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;

            $logoPath = public_path('images/logo/logotype-light.png');
            if (file_exists($logoPath)) {
                $this->mailer->addEmbeddedImage($logoPath, 'logo_cid');
            }

            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = $altBody;
            $this->mailer->send();
        } catch (Exception $e) {
            Log::error("Failed to send email to {$to}: " . $e->getMessage());
            throw $e;
        }
    }
}
