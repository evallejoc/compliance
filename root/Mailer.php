<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/PHPMailer/src/Exception.php';
require __DIR__ . '/vendor/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/vendor/PHPMailer/src/SMTP.php';

class Mailer {
    private $smtp;

    public function __construct($smtpConfig) {
        $this->smtp = $smtpConfig;
    }

    public function send($to, $subject, $body, $attachment = null) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $this->smtp['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtp['username'];
            $mail->Password   = $this->smtp['password'];
            $mail->SMTPSecure = $this->smtp['secure'];
            $mail->Port       = $this->smtp['port'];

            $mail->setFrom($this->smtp['username'], 'Formulario Web');
            $mail->addAddress($to);

            if ($attachment) {
                $mail->addStringAttachment(
                    $attachment['content'],
                    $attachment['filename'],
                    'base64',
                    'application/json'
                );
            }

            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
