<?php

class logger {

    static array $errs;
    static private $logfile = ROOT . 'log.txt';

    public static function add(string $e) {
        self::$errs [] = $e;
    }

    public static function put(string $str) {
        file_put_contents(self::$logfile, $str, FILE_APPEND);
    }

}

function evaErrorCode($param = 200) {
    static $code = 200;
    $res = $code;
    $code = (int) $param;

    if ($res != 200 && Portal::$cur->mail_errors) {
        $template = ['template' =>
        array(
            'id' => ID_TEMPLATE_ERROR,
            'variables' =>
            array(
                'sp_subject' => 'ОШИБКА :' . $res
            ),
        )];
        xmail(['bot@laxo.one'], 'Ошибка', "", FROM, $template);
    }

    return $res;
}

function evaErrorCodeFalse($param = 200) {
    evaErrorCode($param);
    return FALSE;
}

//May be replaced by mongodb 
function object_save($data, string $name, string $root = OBJ_ROOT) {
    return file_put_contents($root . $name, json_encode($data));
}

function object_get(string $name, string $root = OBJ_ROOT) {
    return json_decode(file_get_contents($root . $name));
}

function object_remove(string $name, string $root = OBJ_ROOT) {
    return unlink($root . $name);
}

function object_exist(string $name, string $root = OBJ_ROOT) {
    return file_exists($root . $name);
}
