<?php
/**
 * Sanskar AI - Mail Service
 * ==========================
 * Email sending via Gmail SMTP (STARTTLS on port 587).
 */

namespace App\Services;

class MailService
{
    private string $fromName;
    private string $fromEmail;
    private string $smtpHost;
    private int    $smtpPort;
    private string $smtpUser;
    private string $smtpPass;
    private int    $smtpTimeout;
    private bool   $debugLog;
    private string $logFile;

    public function __construct()
    {
        $this->fromName  = $_ENV['APP_NAME']          ?? 'Sanskar AI';
        $this->fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
        $this->smtpHost  = $_ENV['MAIL_HOST']         ?? 'smtp.gmail.com';
        $this->smtpPort  = (int)($_ENV['MAIL_PORT']   ?? 587);
        $this->smtpUser  = $_ENV['MAIL_USERNAME']     ?? '';
        $this->smtpPass  = $_ENV['MAIL_PASSWORD']     ?? '';
        $this->smtpTimeout = (int)($_ENV['MAIL_TIMEOUT'] ?? 30);
        $this->debugLog = strtolower((string)($_ENV['MAIL_DEBUG_LOG'] ?? 'false')) === 'true';
        $defaultLogFile = 'storage/logs/mail.log';
        $this->logFile = (string)($_ENV['MAIL_LOG_FILE'] ?? $defaultLogFile);
    }

    /**
     * Send plain-text email
     */
    public function send(string $to, string $subject, string $body): bool
    {
        return $this->sendViaSMTP($to, $subject, $body, 'text/plain');
    }

    /**
     * Send HTML email
     */
    public function sendHtml(string $to, string $subject, string $body): bool
    {
        return $this->sendViaSMTP($to, $subject, $body, 'text/html');
    }

    /**
     * Send booking notification to a pandit when a user books them
     *
     * @param string $panditEmail  Pandit's email address
     * @param string $panditName   Pandit's name
     * @param array  $booking      Booking details
     * @param array  $user         User (booker) details
     * @return bool
     */
    public function sendPanditBookingNotification(
        string $panditEmail,
        string $panditName,
        array  $booking,
        array  $user
    ): bool {
        $appName = $_ENV['APP_NAME'] ?? 'Sanskar AI';
        $appUrl  = $_ENV['APP_URL']  ?? '';

        $subject = "New Booking Request – {$appName}";

        $purpose = !empty($booking['ritual_name'])
            ? htmlspecialchars($booking['ritual_name'])
            : htmlspecialchars($booking['booking_purpose'] ?? 'Not specified');

        $date    = !empty($booking['scheduled_date'])
            ? date('d M Y', strtotime($booking['scheduled_date']))
            : 'Not specified';

        $time    = !empty($booking['scheduled_time'])
            ? date('h:i A', strtotime($booking['scheduled_time']))
            : 'Not specified';

        $venue   = !empty($booking['venue'])
            ? htmlspecialchars($booking['venue'])
            : 'Not specified';

        $notes   = !empty($booking['user_notes'])
            ? htmlspecialchars($booking['user_notes'])
            : 'None';

        $userName   = htmlspecialchars($user['name']   ?? 'Customer');
        $userEmail  = htmlspecialchars($user['email']  ?? '');
        $userMobile = htmlspecialchars($user['mobile'] ?? 'Not provided');

        $panditNameSafe = htmlspecialchars($panditName);
        $loginUrl       = rtrim($appUrl, '/') . '/pandit/booking-requests';

        $body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Booking Request</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

          <!-- Header -->
          <tr>
            <td style="background:#b45309;padding:28px 32px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:22px;letter-spacing:0.5px;">{$appName}</h1>
              <p style="margin:6px 0 0;color:#fde68a;font-size:14px;">Pandit Booking Notification</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:32px;">
              <p style="margin:0 0 16px;font-size:15px;color:#374151;">Namaste <strong>{$panditNameSafe}</strong>,</p>
              <p style="margin:0 0 24px;font-size:15px;color:#374151;">
                You have received a new booking request. Please review the details below and confirm or reject the booking from your dashboard.
              </p>

              <!-- Booking Details Box -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border:1px solid #fde68a;border-radius:6px;margin-bottom:24px;">
                <tr>
                  <td style="padding:20px;">
                    <h2 style="margin:0 0 16px;font-size:16px;color:#92400e;border-bottom:1px solid #fde68a;padding-bottom:10px;">Booking Details</h2>
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr><td style="padding:5px 0;font-size:14px;color:#6b7280;width:40%;">Purpose / Ritual</td><td style="padding:5px 0;font-size:14px;color:#111827;font-weight:600;">{$purpose}</td></tr>
                      <tr><td style="padding:5px 0;font-size:14px;color:#6b7280;">Date</td><td style="padding:5px 0;font-size:14px;color:#111827;font-weight:600;">{$date}</td></tr>
                      <tr><td style="padding:5px 0;font-size:14px;color:#6b7280;">Time</td><td style="padding:5px 0;font-size:14px;color:#111827;font-weight:600;">{$time}</td></tr>
                      <tr><td style="padding:5px 0;font-size:14px;color:#6b7280;">Venue</td><td style="padding:5px 0;font-size:14px;color:#111827;">{$venue}</td></tr>
                      <tr><td style="padding:5px 0;font-size:14px;color:#6b7280;">Notes</td><td style="padding:5px 0;font-size:14px;color:#111827;">{$notes}</td></tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Customer Details Box -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;margin-bottom:28px;">
                <tr>
                  <td style="padding:20px;">
                    <h2 style="margin:0 0 16px;font-size:16px;color:#166534;border-bottom:1px solid #bbf7d0;padding-bottom:10px;">Customer Information</h2>
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr><td style="padding:5px 0;font-size:14px;color:#6b7280;width:40%;">Name</td><td style="padding:5px 0;font-size:14px;color:#111827;font-weight:600;">{$userName}</td></tr>
                      <tr><td style="padding:5px 0;font-size:14px;color:#6b7280;">Email</td><td style="padding:5px 0;font-size:14px;color:#111827;">{$userEmail}</td></tr>
                      <tr><td style="padding:5px 0;font-size:14px;color:#6b7280;">Mobile</td><td style="padding:5px 0;font-size:14px;color:#111827;">{$userMobile}</td></tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- CTA Button -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center" style="padding-bottom:24px;">
                    <a href="{$loginUrl}" style="display:inline-block;background:#b45309;color:#ffffff;text-decoration:none;padding:12px 32px;border-radius:6px;font-size:15px;font-weight:600;">View &amp; Respond to Booking</a>
                  </td>
                </tr>
              </table>

              <p style="margin:0;font-size:13px;color:#9ca3af;text-align:center;">
                If you did not expect this email, please ignore it or contact support.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f9fafb;padding:16px 32px;text-align:center;border-top:1px solid #e5e7eb;">
              <p style="margin:0;font-size:12px;color:#9ca3af;">&copy; {$appName}. All rights reserved.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        return $this->sendHtml($panditEmail, $subject, $body);
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

        $body = "Hello,\n\n"
            . "We received a request to reset your {$appName} account password.\n\n"
            . "To create a new password, click the link below (or paste it into your browser):\n"
            . "{$resetLink}\n\n"
            . "This link will expire in 30 minutes for your security.\n\n"
            . "If you did not request a password reset, you can safely ignore this email.\n"
            . "Your password will remain unchanged.\n\n"
            . "Take care,\n"
            . "The {$appName} Team\n";

        return $this->send($toEmail, $subject, $body);
    }

    /**
     * Notify an admin that a new pandit has signed up and is waiting for approval.
     */
    public function sendAdminPanditSignupNotification(string $adminEmail, array $pandit): bool
    {
        $appName = $_ENV['APP_NAME'] ?? 'Sanskar AI';
        $appUrl  = rtrim($_ENV['APP_URL'] ?? '', '/');

        $panditName = htmlspecialchars((string) ($pandit['name'] ?? 'New Pandit'));
        $panditEmail = htmlspecialchars((string) ($pandit['email'] ?? ''));
        $panditMobile = htmlspecialchars((string) ($pandit['mobile'] ?? ''));
        $communityName = htmlspecialchars((string) ($pandit['community_name'] ?? 'Not provided'));
        $specialization = htmlspecialchars((string) ($pandit['specialization'] ?? 'General Puja'));
        $experienceYears = (int) ($pandit['experience_years'] ?? 0);
        $bio = trim((string) ($pandit['bio'] ?? ''));
        $bioSafe = $bio !== '' ? nl2br(htmlspecialchars($bio)) : 'Not provided';

        $pendingUrl = $appUrl !== '' ? $appUrl . '/admin/pandits/pending' : '/admin/pandits/pending';

        $subject = "New Pandit Signup Pending Approval - {$appName}";

        $body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Pandit Signup</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
          <tr>
            <td style="background:#7c3aed;padding:24px 30px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:20px;">{$appName}</h1>
              <p style="margin:8px 0 0;color:#ede9fe;font-size:14px;">New Pandit Approval Request</p>
            </td>
          </tr>
          <tr>
            <td style="padding:30px;">
              <p style="margin:0 0 16px;color:#374151;font-size:15px;">A new pandit has signed up and is waiting for admin approval.</p>

              <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf5ff;border:1px solid #ddd6fe;border-radius:6px;">
                <tr>
                  <td style="padding:18px;">
                    <h2 style="margin:0 0 12px;font-size:16px;color:#5b21b6;">Pandit Details</h2>
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr><td style="padding:5px 0;color:#6b7280;font-size:14px;width:38%;">Name</td><td style="padding:5px 0;color:#111827;font-size:14px;font-weight:600;">{$panditName}</td></tr>
                      <tr><td style="padding:5px 0;color:#6b7280;font-size:14px;">Email</td><td style="padding:5px 0;color:#111827;font-size:14px;">{$panditEmail}</td></tr>
                      <tr><td style="padding:5px 0;color:#6b7280;font-size:14px;">Mobile</td><td style="padding:5px 0;color:#111827;font-size:14px;">{$panditMobile}</td></tr>
                      <tr><td style="padding:5px 0;color:#6b7280;font-size:14px;">Community</td><td style="padding:5px 0;color:#111827;font-size:14px;">{$communityName}</td></tr>
                      <tr><td style="padding:5px 0;color:#6b7280;font-size:14px;">Specialization</td><td style="padding:5px 0;color:#111827;font-size:14px;">{$specialization}</td></tr>
                      <tr><td style="padding:5px 0;color:#6b7280;font-size:14px;">Experience</td><td style="padding:5px 0;color:#111827;font-size:14px;">{$experienceYears} years</td></tr>
                      <tr><td style="padding:5px 0;color:#6b7280;font-size:14px;vertical-align:top;">Bio</td><td style="padding:5px 0;color:#111827;font-size:14px;">{$bioSafe}</td></tr>
                    </table>
                  </td>
                </tr>
              </table>

              <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:22px;">
                <tr>
                  <td align="center">
                    <a href="{$pendingUrl}" style="display:inline-block;background:#7c3aed;color:#fff;text-decoration:none;padding:12px 26px;border-radius:6px;font-size:14px;font-weight:600;">Review Pending Pandits</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        return $this->sendHtml($adminEmail, $subject, $body);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private: raw SMTP sender (STARTTLS, Gmail compatible)
    // ─────────────────────────────────────────────────────────────────────────

    private function sendViaSMTP(
        string $toEmail,
        string $subject,
        string $body,
        string $contentType = 'text/html'
    ): bool {
      $startedAt = microtime(true);
        $host       = $this->smtpHost;
        $port       = $this->smtpPort;
        $user       = $this->smtpUser;
        $pass       = $this->smtpPass;
        $from       = $this->fromEmail ?: $user;
        $fromName   = $_ENV['MAIL_FROM_NAME'] ?? $this->fromName;
        $localDomain = $_SERVER['SERVER_NAME'] ?? (gethostname() ?: 'localhost');
        $timeout    = $this->smtpTimeout > 0 ? $this->smtpTimeout : 30;

      $this->writeLog('SEND_START', [
        'to' => $toEmail,
        'subject' => $subject,
        'host' => $host,
        'port' => $port,
        'user' => $this->maskEmail($user),
        'from' => $from,
        'timeout' => $timeout,
        'contentType' => $contentType,
      ]);

        // ── Pre-flight: OpenSSL required for STARTTLS ──
        if (!extension_loaded('openssl')) {
        $this->writeLog('OPENSSL_MISSING', ['message' => 'PHP openssl extension is not loaded']);
            error_log('MailService SMTP: PHP openssl extension is not loaded – cannot use TLS');
            return false;
        }

        // ── Open plain TCP connection (STARTTLS upgrades it later) ──
        $socket = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            $timeout
        );
        if (!$socket) {
          $this->writeLog('CONNECT_FAIL', [
            'host' => $host,
            'port' => $port,
            'errno' => $errno,
            'error' => $errstr,
          ]);
            error_log("MailService SMTP: cannot connect to {$host}:{$port} – {$errstr} ({$errno})");
            return false;
        }
        $this->writeLog('CONNECT_OK', ['host' => $host, 'port' => $port]);
        stream_set_timeout($socket, $timeout);

        // Helper: read a full SMTP response (handles multi-line "250-…" continuations)
        $read = function () use ($socket): string {
            $data = '';
            while ($line = fgets($socket, 1024)) {
                $data .= $line;
                // Last continuation line has a space at position 3: "250 OK"
                if (strlen($line) >= 4 && $line[3] === ' ') {
                    break;
                }
            }
            return $data;
        };

        // Helper: write a command
          $cmd = function (string $line) use ($socket): void {
            fwrite($socket, $line . "\r\n");
        };

        // ── SMTP handshake ──

        // 1. Server greeting
        $resp = $read();
        $this->writeLog('SERVER_GREETING', ['response' => $this->clipResponse($resp)]);
        if (strpos($resp, '220') === false) {
          $this->writeLog('SERVER_GREETING_FAIL', ['response' => $this->clipResponse($resp)]);
            fclose($socket);
            return false;
        }

        // 2. EHLO
        $cmd("EHLO {$localDomain}");
        $resp = $read();
        $this->writeLog('EHLO', ['response' => $this->clipResponse($resp)]);

        // 3. STARTTLS
        $cmd("STARTTLS");
        $resp = $read();
        $this->writeLog('STARTTLS', ['response' => $this->clipResponse($resp)]);
        if (strpos($resp, '220') === false) {
            error_log("MailService SMTP: STARTTLS refused – {$resp}");
            fclose($socket);
            return false;
        }

        // 4. Upgrade stream to TLS
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
          $this->writeLog('TLS_NEGOTIATION_FAIL', ['message' => 'stream_socket_enable_crypto returned false']);
            error_log('MailService SMTP: TLS negotiation failed');
            fclose($socket);
            return false;
        }
        $this->writeLog('TLS_NEGOTIATION_OK', ['message' => 'TLS channel established']);

        // 5. EHLO again over TLS
        $cmd("EHLO {$localDomain}");
        $resp = $read();
        $this->writeLog('EHLO_TLS', ['response' => $this->clipResponse($resp)]);

        // 6. AUTH LOGIN
        $cmd("AUTH LOGIN");
        $resp = $read();
        $this->writeLog('AUTH_LOGIN_CHALLENGE', ['response' => $this->clipResponse($resp)]);
        $cmd(base64_encode($user));
        $resp = $read();
        $this->writeLog('AUTH_USER_ACCEPTED', ['response' => $this->clipResponse($resp)]);
        $cmd(base64_encode($pass));
        $resp = $read();
        $this->writeLog('AUTH_PASSWORD_RESULT', ['response' => $this->clipResponse($resp)]);
        if (strpos($resp, '235') === false) {
            error_log("MailService SMTP: AUTH failed – {$resp}");
            fclose($socket);
            return false;
        }

        // 7. MAIL FROM
        $cmd("MAIL FROM:<{$from}>");
        $resp = $read();
        $this->writeLog('MAIL_FROM', ['response' => $this->clipResponse($resp)]);

        // 8. RCPT TO
        $cmd("RCPT TO:<{$toEmail}>");
        $resp = $read();
        $this->writeLog('RCPT_TO', ['response' => $this->clipResponse($resp)]);
        if (strpos($resp, '250') === false) {
            error_log("MailService SMTP: RCPT rejected – {$resp}");
            fclose($socket);
            return false;
        }

        // 9. DATA command
        $cmd("DATA");
        $resp = $read();
        $this->writeLog('DATA_COMMAND', ['response' => $this->clipResponse($resp)]);
        if (strpos($resp, '354') === false) {
          $this->writeLog('DATA_COMMAND_FAIL', ['response' => $this->clipResponse($resp)]);
            fclose($socket);
            return false;
        }

        // 10. Build RFC 2822 message
        $msgId          = '<' . time() . '.' . mt_rand() . '@' . $localDomain . '>';
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedFrom    = '=?UTF-8?B?' . base64_encode($fromName) . '?=';
        $encodedBody    = chunk_split(base64_encode($body));

        $message  = "Date: " . date('r') . "\r\n";
        $message .= "From: {$encodedFrom} <{$from}>\r\n";
        $message .= "To: {$toEmail}\r\n";
        $message .= "Subject: {$encodedSubject}\r\n";
        $message .= "Message-ID: {$msgId}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: {$contentType}; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "\r\n";
        $message .= $encodedBody;
        $message .= "\r\n.\r\n";   // end-of-data marker

        fwrite($socket, $message);
        $resp = $read();   // 250 2.0.0 OK – message accepted
        $this->writeLog('MESSAGE_ACCEPTANCE', ['response' => $this->clipResponse($resp)]);

        // 11. QUIT
        $cmd("QUIT");
        $this->writeLog('QUIT_SENT', ['message' => 'QUIT command sent']);
        fclose($socket);

        $ok = strpos($resp, '250') !== false;
        if (!$ok) {
            error_log("MailService SMTP: message not accepted – {$resp}");
        }
        $this->writeLog('SEND_RESULT', [
          'success' => $ok,
          'durationMs' => (int) round((microtime(true) - $startedAt) * 1000),
        ]);
        return $ok;
    }

      private function writeLog(string $event, array $context = []): void
      {
        if (!$this->debugLog) {
          return;
        }

        $basePath = dirname(__DIR__, 2);
        $logPath = $this->logFile;
        if (!preg_match('#^[A-Za-z]:[\\/]#', $logPath) && strpos($logPath, '/') !== 0) {
          $logPath = $basePath . '/' . ltrim($logPath, '/\\');
        }

        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
          @mkdir($logDir, 0775, true);
        }

        $line = sprintf(
          "[%s] [%s] %s%s",
          date('Y-m-d H:i:s'),
          $event,
          json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
          PHP_EOL
        );

        @file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX);
      }

      private function clipResponse(string $response, int $maxLen = 500): string
      {
        $normalized = trim(preg_replace('/\s+/', ' ', $response));
        if (strlen($normalized) <= $maxLen) {
          return $normalized;
        }

        return substr($normalized, 0, $maxLen) . '...';
      }

      private function maskEmail(string $email): string
      {
        $email = trim($email);
        if ($email === '' || strpos($email, '@') === false) {
          return 'n/a';
        }

        [$name, $domain] = explode('@', $email, 2);
        if ($name === '') {
          return '***@' . $domain;
        }

        $visible = strlen($name) > 2 ? substr($name, 0, 2) : substr($name, 0, 1);
        return $visible . '***@' . $domain;
      }
}
