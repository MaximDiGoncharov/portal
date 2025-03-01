<?php

use FTP\Connection;

function xmail(string|array $to, string $subject, string $message = "", string $from = FROM, array|null $template = NULL)
{
    $res = null;
    $data = array(
        'email' =>
        array(
            'subject' => $subject,
            'from' =>
            array(
                'name' => 'Laxo CRM',
                'email' => $from,
            ),
            'to' =>
            array(
                0 =>
                array(
                    'name' => '',
                    'email' => $to,
                ),
            ),
        ),
    );

    if (is_string($to)) {
        $data['email']['to'] = [];
        $user = array(
            'name' => '',
            'email' => $to,
        );
        $data['email']['to'][] = $user;
    }
    if (is_array($to)) {
        $data['email']['to'] = [];
        foreach ($to as $i) {
            if (!is_null($i['user_email'])) {
                $user = array(
                    'name' => '',
                    'email' => $i['user_email'],
                );
                array_push($data['email']['to'], $user);
            }
        };
    }
    if (!is_null($template)) {
        $data['email']['template'] = $template['template'];
    }
    if (is_array($data) && count($data)) {
        // uncomment
        $acccesstoken = get_access_token();
        $res = sendpulse_send(SENDPULSE_URL, $data, $acccesstoken);
    }
    return $res;
}

function get_access_token()
{
    $url = SENDPULSE_URL . 'oauth/access_token';
    $data = array(
        'grant_type' => 'client_credentials',
        'client_id' => API_USER_ID,
        'client_secret' => API_SECRET,
    );
    return sendpulse_send($url, $data)['access_token'];
}

function sendpulse_send(string $url, array $data, $token = false)
{
    $headers = ['Content-Type:application/json'];
    if ($token) {
        $url .= 'smtp/emails';
        array_push($headers, 'Authorization: Bearer ' . $token);
    } else {
        $url .= 'oauth/access_token';
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($res, JSON_UNESCAPED_UNICODE);
    return $res;
}

function mail_pass(string $email, string $password, string $from = FROM)
{
  $link = '<a style="margin: 0 auto;  display: block; text-decoration: none; padding: 15px 30px; font-size: 15px; text-align: center; font-weight: bold; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; width: 120px; color: #ffffff; border: 0px solid; background-color: #f96967; border-radius: 3px;" href="https://' . $from . 'dbError/"  style="text-decoration:none;color:#0089bf" target="_blank" rel="noopener noreferrer">'.$from . 'dbError</a>';
        ;
    $template = ['template' =>
    array(
        'id' => ID_TEMPLATE_SENDPUlSE,
        'variables' =>
        array(
            'subscriber_id' => $password,
            'email' => $email,
            'host'=>$link
        ),
    )];
    return xmail($email, 'Регистрация', "", FROM, $template);
}

function mail_token(string $mail, $token, $domain = null)
{

    // last link 
    // $link = "<a href='http://artem.laxo.work/activate.html?mail=`{$mail}`&token=`{$token}`&domain=`{$domain}`'  style='text-decoration:none;color:#0089bf' target='_blank'>Активировать</a></center></p>";
    // https://login.sendpulse.com/emailservice/constructor/structure/layout/0/template/159211/
    $link = '<a style="display: table-cell; text-decoration: none; padding: 15px 30px; font-size: 15px; text-align: center; font-weight: bold; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; max-width:250px; width: 100%; color: #ffffff; border: 0px solid; background-color: #f96967; border-radius: 3px;" href="https://' . $domain . 'dbError/activate/' . $mail . '/' . $token . '"  style="text-decoration:none;color:#0089bf" target="_blank" rel="noopener noreferrer">Активировать</a>';


    $template = ['template' =>
    array(
        'id' => ID_TEMPLATE_MAIL_TOKEN,
        'variables' =>
        array(
            'ec_es_email_sender_company' => $domain . 'dbError',
            'subscriber_id' => $link,
            // 'host'=> '<span>' . $domain . 'dbError/activate/' . $mail . '/' . $token .'</span>'
            'host'=> '<a href=\'#\' style=\'text-decoration: none; color:#000000\' >https://' . $domain . 'dbError/activate/' . $mail . '/' . $token .'</a>'
        ),
    )];
    return xmail($mail, 'Активация', "", FROM, $template);
}

function get_imap_connection(array|false $smtp = false)
{
    if ($smtp) {
        
        $smtp['login'] = isset($smtp['imap_login']) ? $smtp['imap_login'] :  $smtp['login'];
        $imap = imap_open(
            $smtp['imap_uri'],
            $smtp['login'],
            $smtp['pass']
        );
    } else {
        $imap = imap_open(
            IMAP_URI,
            IMAP_LOGIN,
            IMAP_PASSWORD
        );
    }

    return $imap;
}

function recieve_mail($imap = null){
    if (is_null($imap)) {
        $imap = get_imap_connection();
    }

    // last code
    $mails = imap_search($imap, 'UNSEEN');

    if ($mails) {
        foreach ($mails as $mail) {
            
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
            else {
                $message =  imap_fetchbody($imap, $mail, 1);
             }
            $from_address = $header->from[0]->mailbox . '@' . $header->from[0]->host;
            $_data = [];
            $header->Subject = imap_utf8($header->Subject);

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
    } else {
    }
}

function recieve_imap_process_by_default($connect = false){
    $count = 0;
    while(true && $count <= 240){
        $imap = get_imap_connection($connect);
        recieve_mail($imap);
        imap_close($imap);
        sleep(15);
        $count++;
    }
    // stop_recieve_imap_process();
    // sleep(1);
    // recieve_imap_process_by_smtp();
}

function start_recieve_imap_process(){
    stop_recieve_imap_process();
    system('/bin/php8.1 -r "require_once \'' . ROOT . 'init.php\';recieve_imap_process();"   &> /dev/null < /dev/null &');
}

function stop_recieve_imap_process(){
    $cmd = 'ps axuw | grep recieve_imap_process | grep -v grep | awk \'{print $2}\' | xargs kill';
    system($cmd);
}


// for receive smtp 

function recieve_imap_process_by_smtp()
{

    $data = glob(APIROOT . 'env/*/obj/user_data_*');
    $data_default =  glob(APIROOT . 'env/*/obj/integration');
    $result = array_merge($data, $data_default);
    foreach ($result as $conf) {
        $conf = (array) @object_get($conf, '')->smtp;
        if ($conf) {
            try {
                $imap = get_imap_connection($conf);
                recieve_mail($imap);
                imap_close($imap);
            } catch (Throwable $e) {
                file_put_contents("/home/maxim/repos/portal/www/imap.txt",$e->getMessage() . " - \n", FILE_APPEND);
                file_put_contents("/home/maxim/repos/portal/www/imap.txt",$e->getFile() . " - \n", FILE_APPEND);
                file_put_contents("/home/maxim/repos/portal/www/imap.txt",$e->getLine() . " - \n", FILE_APPEND);
            }
        }
    }
}
// function start_recieve_imap_process_by_smtp(){
//     stop_recieve_imap_process_smtp();
//     system('/bin/php8.1 -r "require_once \'' . ROOT . 'init.php\';recieve_imap_process_by_smtp();"   &> /dev/null < /dev/null &');
// }

// function stop_recieve_imap_process_smtp() {
//     $cmd = 'ps axuw | grep start_recieve_imap_process_by_smtp | grep -v grep | awk \'{print $2}\' | xargs kill';
//     system($cmd);
// }
