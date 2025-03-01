<?php

function route() {
    $json = file_get_contents('php://input');
    $json_out = remoteInit($json);
    header('Content-Type: application/json');
    echo $json_out;
}

function remoteInit($param) {
    $obj = json_decode($param);
    $sidP = null;

    if (isset($obj[0]) && isset($obj[0]->{'sid'})) {
        session::start($sidP = $obj[0]->{'sid'});
    } 
    elseif (isset($obj[0]) && isset($obj[0]->{'wp_sid'})) {
        wp_session::start($obj[0]->{'wp_sid'});
    }

    $answ = [];
    for ($i = 0; isset($obj[$i]); $i++) {
        try {

            $class = $obj[$i]->{'class'};
            $method = $obj[$i]->method;

            if (array_key_exists($class, Portal::$api_methods) && in_array($method, Portal::$api_methods[$class])) {
                $paramer = [];
                if (isset($obj[$i]->param)) {
                    $paramer = $obj[$i]->param;
                    if (is_object($paramer)) {
                        $paramer = (array) $paramer;
                    }
                }
                $ans = $class :: $method($paramer);
                $ans = ['code' => evaErrorCode(), 'response' => $ans];


                $answ [] = $ans;
            } else {
                logger::add('Unavailable method is requested ' . $class . '::' . $method);
            }
        } catch (Exception $e) {
            $ans = ['code' => 100, 'response' => null];
            logger::add(__FILE__ . ': ' . __LINE__ . 'Catched exeption: ' . $e->getMessage() . ' `File: ' . $e->getFile() . ' LINE:' . $e->getLine() . ' ' . var_export($e->getTrace(), true));
        }
    }



    $code = 200;
    $resp = [];

    if (isset(logger::$errs)) {
        $resp['errs'] = logger::$errs;
    }

    if (!session::is_empty()) {
        $resp['sid'] = session::$sid;
        session::close();
    }
    $answ [] = ['code' => $code, 'response' => $resp];

    $res = json_encode($answ);


    if (!$res) {
        logger::put('Serios problem: json is null but var_export of res is: ' . var_export($res, true));
        array_walk_recursive($answ, 'check_infinite_validate');
        $res = json_encode($answ);
    }

    if (Portal::$cur->db_logging) {
        $str_log = PHP_EOL . PHP_EOL . PHP_EOL . 'time:' . time() . PHP_EOL . 'INPUT:' . $param . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . 'OUTPUT:' . $res;
        logger::put($str_log);
    }
    return $res;
}

function file_route()
{

    $referer = $_SERVER['HTTP_REFERER'];


    $input = filter_input(INPUT_SERVER, 'REQUEST_URI');
    $arr_input = explode('/', $input);


    if ($arr_input[1] == 'uploads') {
        try {

            field_file::downloader($arr_input);
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    } else if ($arr_input[1] == 'upload') {
        field_file::uploader($arr_input, $input);
    }
}
