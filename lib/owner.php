<?php


class owner
{

    public static $filds = ['user_id', 'user_email', 'user_name', 'user_register_date', 'user_born_date', 'user_country', 'user_source', 'user_phone', 'user_domain', 'user_company_id',  'user_pay_account_id', 'user_activation_str', 'user_ip_create', 'version_id', 'date_activate', 'date_deactivate', 'date_need_payed', 'tarif_id', 'tarif_person_count', 'tarif_price', 'tarif_description'];
    static public $instanse = ['date_need_payed', 'tarif_person_count'];

    static function obj(): db_object
    {
        return new db_object('user', ['user_email', 'user_name', 'user_country', 'user_activation_str', 'user_phone'], ['user_email', 'user_name']);
    }

    public static  function get_list($id, $admin = false, $condition = '')
    {
        if (wp_session::check_auth() || $admin) {
            $res = [];
            $sql = 'SELECT * FROM user ';
            if ($id) {
                $cond = ' WHERE user_id= ' . db_safe($id);
            }

            $sql .= $cond;
            $sql .= $condition;
            foreach (db($sql) as $r) {
                $instanse = @object_get('env/' . $r['user_domain'] . '/instanse.config.json', APIROOT);
                $r['version_id'] = $instanse->version_id;
                $tarif = (array)$instanse->tarif;
                if (!count($tarif)) {
                    $tarif['is_exist'] = false;
                }
                $tarif['count_user'] = count(glob('/home/maxim/repos/api/env/' . $r['user_domain'] . '/obj/user_data_*'));
                if ($id) {
                    $conn_to_crm = mysqli_connect($instanse->db->host, $instanse->db->user, $instanse->db->pass, $instanse->db->name);
                    $sql = 'SELECT notification_date_send FROM notification ORDER BY notification_id DESC LIMIT 1';
                    $result = mysqli_query($conn_to_crm, $sql);
                    $result = mysqli_fetch_row($result);
                    $tarif['user_last_action'] =  $result[0];
                } else {
                    $tarif['user_last_action'] = file_get_contents('/home/maxim/repos/portal/www/files/get_list_last_action.json');
                    $tarif['user_last_action'] = (array) json_decode($tarif['user_last_action']);
                    $tarif['user_last_action'] =   $tarif['user_last_action'][$r['user_domain']];
                    $tarif['user_last_action'] =  date('Y-m-d', $tarif['user_last_action']);
                }



                $res[] = array_merge($r, $tarif);
            }
        }
        return $res;
    }

    public static function change($param)
    {
        if (wp_session::check_auth()) {
            $f_instanse = null;
            foreach ($param as $key => $value) {
                if (in_array($key, self::$instanse) && !is_null($value)) {
                    $f_instanse[$key] = $value;
                }
            }
            $file_res = [];
            self::obj()->_up($param);

            if ($f_instanse && $param['user_domain']) {
                $instanse_obj = object_get('env/' . $param['user_domain'] . '/instanse.config.json', APIROOT);
                foreach ($f_instanse as $key => $value) {
                    $instanse_obj->tarif->$key = $value;
                    $file_res[] = $instanse_obj->tarif->$key;
                }
                $file_res[] =  object_save($instanse_obj, 'env/' . $param['user_domain'] . '/instanse.config.json', APIROOT);
            }
        }
        return [$param['user_id'], $file_res];
    }

    public static function delete($param)
    {
        if (portal::check_domain_exist(...$param)) {
            portal::drop(...$param);
        }
    }

    public static function get_users_of_crm(string $domain)
    {
        if (wp_session::check_auth()) {
            $props = object_get('env/' . $domain . '/instanse.config.json', APIROOT);
            if ($props) {
                portal::init($props);
                $res = user_of_owner::get_list();
                $contact = user_of_owner::get_contact_list();
                $order = user_of_owner::get_order_list();
                return ['users' => $res, 'contact' =>  $contact, 'order' => $order];
            }
        }
    }
    public static function get_email_logs(string $domain)
    {
        if (wp_session::check_auth()) {
            try {

                $res = object_get('env/' . $domain . '/logs/email_notification.json', APIROOT);
                if (!$res) {
                    return false;
                }
                $result = [];
                foreach ($res as $row) {
                    $result[] = [
                        $row->users,
                        $row->time,
                        $row->obj,
                        $row->obj_id,
                        $row->text
                    ];
                }
                return $result;
            } catch (Throwable $e) {
                echo $e->getMessage();
                echo $e->getLine();
            }
        }
    }

    public static function get_push_logs(string $domain)
    {
        if (wp_session::check_auth()) {
            try {

                $res = object_get('env/' . $domain . '/logs/push_notification.json', APIROOT);
                if (!$res) {
                    return false;
                }
                $result = [];
                foreach ($res as $row) {
                    $result[] = [
                        $row->users,
                        $row->time,
                        $row->obj,
                        $row->obj_id,
                        $row->text
                    ];
                }

                return $result;
            } catch (Throwable $e) {
                echo $e->getMessage();
                echo $e->getLine();
            }
        }
    }


    public static function get_list_last_action()
    {
        $res = [];

        $sql = 'SELECT * FROM user ';
        $all_crm_name = db_run($sql);

        foreach ($all_crm_name as $crm_name) {
            $instanse = @object_get('env/' . $crm_name['user_domain'] . '/instanse.config.json', APIROOT);
            try {

                $conn_to_crm = mysqli_connect(
                    $instanse->db->host,
                    $instanse->db->user,
                    $instanse->db->pass,
                    $instanse->db->name
                );
                $sql = 'SELECT notification_date_send FROM notification ORDER BY notification_id DESC LIMIT 1';
                $result = mysqli_query($conn_to_crm, $sql);
                $result = mysqli_fetch_row($result);
                if (!empty($result)) {
                    $res[$crm_name['user_domain']] = $result[0];
                }
            } catch (Throwable $e) {
                echo $crm_name['user_domain'] . '/';
                echo $e->getMessage();
            }
        }
        file_put_contents('/home/maxim/repos/portal/www/files/get_list_last_action.json', json_encode($res));
        return $res;
    }
}


class user_of_owner
{
    public static $filds = ['user_id', 'user_email', 'user_login', 'user_password', 'user_contact_id', 'user_token', 'user_token_expire', 'user_active', 'user_role_id'];

    static function obj(): db_object
    {
        return new db_object('user', ['user_id', 'user_email', 'user_login', 'user_password', 'user_contact_id', 'user_token', 'user_token_expire', 'user_active', 'user_role_id'], ['user_email', 'user_login', 'user_password', 'user_active', 'user_role_id']);
    }

    public static function get_list($id = false)
    {
        if (wp_session::check_auth()) {
            $sql = 'select `user`.`user_id`,`user`.user_login,`user`.user_email, user_password, user_token,user_token_expire,user_active, user.user_role_id from `user`';
            if ($id) {
                $sql .= ' WHERE `user`.user_id=' . db_safe($id);
            }
            $res = db_run($sql);
        }
        return $res;
    }

    public static function change($param, $admin = false)
    {
        if (wp_session::check_auth() || $admin) {
            $props = object_get('env/' . $param['crm_name'] . '/instanse.config.json', APIROOT);
            if ($props) {
                portal::init($props);
                if (isset($param['user_password'])) {
                    mail_pass($param['user_email'], $param['user_password'], $param['crm_name']);
                    $param['user_password'] = md5($param['user_password']);
                }
                $user_id = self::obj()->_up($param);
                return $user_id;
            }
        }
    }


    public static function get($param, $admin = true)
    {
        // method for auth from crm to laxo.one!
        if (wp_session::check_auth() || $admin) {
            $props = object_get('env/' . $param['crm_name'] . '/instanse.config.json', APIROOT);
            if ($props) {
                portal::init($props);
                $sql = 'SELECT user.user_id, user.user_email,  contact_name, contact_id FROM user INNER JOIN contact ON user_contact_id = contact_id 
                WHERE 
                user_id = (SELECT user_id FROM `session` WHERE session_id=UNHEX("' . $param['sid'] . '") LIMIT 1) LIMIT 1';
                $res = db_run_row($sql);

                if (!isset($res['contact_id'])) {
                    return false;
                }
                $field_avatar_id = 9;
                $sql = 'SELECT field_char_value.field_value_id  FROM contact_has_field_value 
                INNER JOIN field_value ON field_value.field_value_id = contact_has_field_value.field_value_id AND field_value.field_id=' . $field_avatar_id . '
                INNER JOIN field ON field.field_id = field_value.field_id AND field.field_id=' .  $field_avatar_id . ' INNER JOIN field_char_value ON field_char_value.field_value_id = field_value.field_value_id';
                $sql .= ' WHERE contact_id = ' . $res['contact_id'];

                $img = db_run_value($sql);
                $res['link_img'] = 'https://' . $param['crm_name'] . 'dbError/uploads/' . $img . '?view=1';
                $res['crm_name'] = $param['crm_name'];
                return $res;
            }
        }
    }

    public static function get_contact_list()
    {
        $sql = 'SELECT * FROM contact';
        return db_run($sql);
    }
    public static function get_order_list()
    {
        $sql = 'SELECT * FROM `order`';
        return db_run($sql);
    }
}

class wp_session
{
    public static $sid = false;
    public static function start(string $sid)
    {
        $wp_sid = file_get_contents(WPROOT . 'wp-admin/crm-admin/sid');
        if ($wp_sid == md5($sid . 'laxo.one')) {
            self::$sid = $sid;
        }
    }
    public static function check_auth()
    {
        return self::$sid;
    }
}
