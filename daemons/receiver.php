<?php
require_once('../init.php');

while (true) {
    $imap = imap_open(
        "{imap.mail.ru:993/imap/ssl}INBOX",
        "bot@admin.com",
        '123456'
    );
    // all crm
    $mails = imap_search($imap, 'NEW');
    $data = [];
    if ($mails)
        foreach ($mails as $mail) {

            file_put_contents("/home/maxim/repos/portal/www/stdout.txt", date("D M j G:i:s") . " - email\n", FILE_APPEND);
            $header = imap_headerinfo($imap, $mail);
            $structure = imap_fetchstructure($imap, $mail);
            if (isset($structure->parts[1])) {
                $part = $structure->parts[1];
                $message = imap_fetchbody($imap, $mail, 1);
                if (strpos($message, '<html') !== false) {
                    $message = trim(utf8_encode(quoted_printable_decode($message)));
                } else if ($part->encoding == 3) {
                    $message = imap_base64($message);
                } else if ($part->encoding == 2) {
                    $message = imap_binary($message);
                } else if ($part->encoding == 1) {
                    $message = imap_8bit($message);
                } else {
                    $message = trim(utf8_encode(quoted_printable_decode(imap_qprint($message))));
                }
            }
            $from_address = $header->from[0]->mailbox . '@' . $header->from[0]->host;
            $_data = [];
            if (strpos($header->Subject, 'dbError') !== false) {
                $message = explode('>', $message);
                $header = str_replace('Re:', '', $header->Subject);
                $header = explode(',', $header);
                $_data['crm_name'] = trim($header[0]);
                $_data['message_to_id'] = trim($header[1]);
                $_data['chat_id'] = trim($header[2]);
                $_data['message_text'] = trim($message[0]);
                $_data['contact_id'] = $from_address;

                Portal::query('message', 'send_from_email', $_data);
            }
        }
    else {
        file_put_contents("/stdout.txt", date("D M j G:i:s") . " - nothing\n", FILE_APPEND);
    }
    imap_close($imap);
    sleep(10);
}
