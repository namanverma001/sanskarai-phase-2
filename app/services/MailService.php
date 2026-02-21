<?php
/**
 * Sanskar AI - Mail Service
 * ==========================
 * Simple email sending service using PHP's mail() function.
 */

namespace App\Services;

class MailService
{
    private string $fromName;
    private string $fromEmail;

    public function __construct()
    {
        $this->fromName  = $_ENV['APP_NAME'] ?? 'Sanskar AI';
        $this->fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? 'info@deocdfai.com';
    }

    /**
     * Send plain-text email
     *
     * @param string $to      Recipient email
     * @param string $subject Email subject
     * @param string $body    Plain text body
     * @return bool
     */
    public function send(string $to, string $subject, string $body): bool
    {
        $headers  = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "Reply-To: {$this->fromEmail}\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        return mail($to, $subject, $body, $headers);
    }

    /**
     * Send the password-reset email
     *
     * @param string $toEmail   Recipient email
     * @param string $resetLink Full reset URL including token
     * @return bool
     */
    public function sendPasswordReset(string $toEmail, string $resetLink): bool
    {
        $appName = $_ENV['APP_NAME'] ?? 'Sanskar AI';

        $subject = "Reset your {$appName} password";

        $body = "Hello,

We received a request to reset your {$appName} account password.

To create a new password, click the link below (or paste it into your browser):
{$resetLink}

This link will expire in 30 minutes for your security.

If you did not request a password reset, you can safely ignore this email.
Your password will remain unchanged.

Take care,
The {$appName} Team
";

        return $this->send($toEmail, $subject, $body);
    }
}
