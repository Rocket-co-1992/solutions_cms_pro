<?php

namespace Pandao\Common\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class MailUtils
 * - sendMail
 * - getMail
 */

class MailUtils
{
    /**
     * Sends an email using PHPMailer.
     *
     * @param string $recipient_email The recipient's email address.
     * @param string $recipient_name The recipient's name.
     * @param string $subject The subject of the email.
     * @param string $content The email content.
     * @param string $reply_email The reply-to email address (optional).
     * @param string $reply_name The reply-to name (optional).
     * @param string $from_email The sender's email address (optional).
     * @param string $from_name The sender's name (optional).
     * @param array $attachements The list of files to attach (optional).
     * @param string $emails_copy The list of emails to send as a copy (optional).
     * @return bool True if the email is sent successfully, false otherwise.
     */
    public static function sendMail($recipient_email, $recipient_name, $subject, $content, $reply_email = '', $reply_name = '', $from_email = '', $from_name = '', $attachements = [], $emails_copy = '')
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        if (PMS_USE_SMTP == 1) {
            $mail->IsSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = PMS_SMTP_HOST;
            $mail->SMTPSecure = PMS_SMTP_SECURITY;
            if (PMS_SMTP_AUTH == 1) {
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                $mail->SMTPAuth = true;
                $mail->Username = PMS_SMTP_USER;
                $mail->Password = PMS_SMTP_PASS;
            }
            $mail->Port = PMS_SMTP_PORT;
        }

        $default_email = (PMS_SENDER_EMAIL != '') ? PMS_SENDER_EMAIL : 'noreply@' . substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'], '.') + 1);
        $default_name = (PMS_SENDER_NAME != '') ? PMS_SENDER_NAME : PMS_SITE_TITLE;

        if ($reply_email == '') $reply_email = $default_email;
        if ($reply_name == '') $reply_name = $default_name;

        if ($from_email == '') $from_email = $default_email;
        if ($from_name == '') $from_name = $default_name;

        $body = '<style>body{background:#dee2e2}</style>
                <center>
                    <div style="width:800px;max-width:100%;margin:20px auto;background:#ffffff;box-shadow:0 0 10px rgba(0,0,0,0.2);">
                        <div style="width:800px;max-width:100%;">
                            <img src="cid:header-mail" alt="">
                        </div>
                        <div style="width:740px;max-width:100%;padding:30px;text-align:left;font-size:14px;color:#333333;">' . StrUtils::htmlAccents($content) . '</div>
                    </div>
                 </center>' . "\n\n";

        try {
            $mail->setFrom(htmlspecialchars_decode($from_email, ENT_QUOTES), htmlspecialchars_decode($from_name, ENT_QUOTES));
            $mail->AddReplyTo(htmlspecialchars_decode($reply_email, ENT_QUOTES), htmlspecialchars_decode($reply_name, ENT_QUOTES));
            $mail->Subject = htmlspecialchars_decode($subject, ENT_QUOTES);
            $mail->AddAddress(htmlspecialchars_decode($recipient_email, ENT_QUOTES), htmlspecialchars_decode($recipient_name, ENT_QUOTES));
            if ($emails_copy != '') {
                $emails_copy = explode(';', $emails_copy);
                foreach ($emails_copy as $email_copy) {
                    if ($email_copy != '') $mail->AddCC($email_copy, '');
                }
            }
            $mail->AddEmbeddedImage(SYSBASE . 'templates/' . PMS_TEMPLATE . '/assets/images/header-mail.png', 'header-mail', 'header-mail.png');
            $mail->MsgHTML($body);
            $mail->AltBody = StrUtils::ripTags($content);

            if (is_array($attachements) && !empty($attachements)) {
                foreach ($attachements as $path) {
                    if (is_file($path)) {
                        $name = substr($path, strrpos($path, '/') + 1);
                        $mime = FileUtils::getFileMimeType($path);
                        $mail->AddAttachment($path, $name, 'base64', $mime);
                    }
                }
            }

            return $mail->Send();

        } catch (Exception $e) {
            echo $e->errorMessage();
            return false;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieves email content from the database and replaces variables.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $name The name of the email template.
     * @param array $vars An associative array of variables to replace in the email content.
     * @return array|bool The email subject and content, or false if not found.
     */
    public static function getMail($db, $name, $vars = array())
    {
        $result = $db->query('SELECT * FROM solutionsCMS_email_content WHERE name = ' . $db->quote($name) . ' AND lang = ' . PMS_LANG_ID);
        if ($result !== false && DbUtils::lastRowCount($db) > 0) {
            $row = $result->fetch();
            $content = $row['content'];

            foreach ($vars as $key => $val) {
                $content = str_replace($key, $val, $content);
            }

            return array('subject' => $row['subject'], 'content' => $content);
        } else {
            return false;
        }
    }
}
