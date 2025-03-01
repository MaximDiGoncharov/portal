<?php

function get_random_token(): string {
    $t = time() . microtime();
    $r = rand();

    $str = md5($t) . md5($r . ip());
    return $str;
}

function get_random_email_token(): string {
    //To easy enter
    $token = rand(1000, 99999);
    return $token;
}

function ip(): string {
    return (string) filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
}

//key not used, but need for array_walk
function check_infinite_validate(&$item, $key) {
    if (is_infinite($item)) {
        $item = PHP_INT_MAX;
    }
}

function start_default_process_by_domain( string|array $domain){
    $domain = (array) $domain;
    foreach($domain as $name){
        $cmd = '$_SERVER["HTTP_HOST"]="https://'.$name.'dbError"; require "/home/maxim/repos/api/init.php"; portal::start_process();';
        system('php -r \'' . $cmd.'\'');
    }
}

function send_request( string $url, array $request_headers, array $fields, string $type = 'POST' ){
    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function exec_migration(string|array $domain,bool $all = false){
    if ($all) {
        $res = owner::get_list(false, true);
        foreach ($res as  $row) {
            sleep(5);
        }
    }
}
