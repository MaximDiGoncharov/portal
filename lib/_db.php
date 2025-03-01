<?php

/* Function for default sql database work */

define('DB_SAFE_NO_QUOTAS', 0);
define('DB_SAFE_QUOTAS', 1);
define('DB_SAFE_COMMA_START', 2);
define('DB_SAFE_QUOTAS_COMMA_START', DB_SAFE_QUOTAS | DB_SAFE_COMMA_START);
define('DB_SAFE_COMMA_END', 4);
define('DB_SAFE_QUOTAS_COMMA_END', DB_SAFE_QUOTAS | DB_SAFE_COMMA_END);
define('DB_SAFE_QUOTAS_COMMAS', DB_SAFE_QUOTAS_COMMA_START | DB_SAFE_QUOTAS_COMMA_END);

function db($query) {
    return portal::$connection->query($query);
}

function db_safe($str, $fl = DB_SAFE_NO_QUOTAS) {

    if ($fl & DB_SAFE_QUOTAS) {
        $str = trim($str);
    }
    if ($str != 'NULL') {
        $str = portal::$connection->real_escape_string($str);
    }
    if ($fl & DB_SAFE_QUOTAS) {
        if ($str != 'NULL') {
            $str = '\'' . $str . '\'';
        }

        if ($fl & DB_SAFE_COMMA_START) {
            $str = ',' . $str;
        }

        if ($fl & DB_SAFE_COMMA_END) {
            $str .= ',';
        }
    }
   

    return $str;
}

function db_error() {
    return portal::$connection->error;
}

function db_id() {
    return affected_rows() > 0 ? portal::$connection->insert_id : 0;
}

function affected_rows() {
    return portal::$connection->affected_rows;
}

function locale_db() {
    portal::$connection->multi_query('SET NAMES utf8;SET time_zone ="+3:00";SET @@lc_time_names = ru_RU');
}

function check_connect() {
    while (!portal::$connection->ping()) {
        sleep(1);
    }
}
function db_run($sql) {
    $res = db($sql);
    $res = mysqli_fetch_all($res, MYSQLI_ASSOC);
    return $res;
}

function db_run_row($sql) {
    $res = db($sql);
    if ($res)
        $res = mysqli_fetch_assoc($res);  //mysqli_fetch_row($res, MYSQLI_ASSOC);
    return $res;
}

function db_run_value($sql) {
    $res = db($sql);
    if ($res) {
        $res = mysqli_fetch_row($res);
        if ($res) {
            $res = $res[0];
        }
    }
    return $res;
}
