<?php

class session {

    static public int $user_id = 0;
    static string $sid = '';
    static string $portal_sid = '';

    static public function init($user_id) {
        self::$user_id = $user_id;
        return self::insert_unique();

    }

    static public function start($sid) {
        if ($sid) {
            $sid = bin2hex(hex2bin($sid));

            $sql = 'SELECT user_id FROM `session` WHERE  session_id=UNHEX("' . $sid . '") LIMIT 1';
            $r = mysqli_fetch_row(db($sql));
            if ($r && (int) $r[0]) {
                self::$user_id = (int) $r[0];
                self::$sid = $sid;
            }
        }

        return self::$sid;
    }

    static public function destroy() {
        $sql = 'DELETE FROM `session` WHERE  session_id=UNHEX("' . self::$sid . '") LIMIT 1';
        db($sql);

        return affected_rows();
    }

    static public function close() {

        if (!self::is_empty()) {
        }
    }

    static function regen($unused_param = false) {
        $uid = self::$user_id;
        self::destroy();
        self::init($uid);
        return !session::is_empty();
    }

    static function binary_session_id() {
        return hex2bin(self::$sid);
    }

    static function is_empty() {
        return self::$sid == '';
    }

    static function insert() {
        $str = self::get_random_token();
        self::$portal_sid = $str;
        $sql = 'INSERT INTO `session`(`session_id`,user_id) VALUES (UNHEX("' . $str . '"),' . self::$user_id . ')';
        db($sql);

        if (affected_rows())
            self::$sid = $str;

        return self::$sid;
    }

    static function insert_unique() {
        for ($i = 0; !self::insert() && $i < 10; $i++)
            ;
        return self::$sid;
    }

    static function get_random_token() {
        return get_random_token();
    }

}
