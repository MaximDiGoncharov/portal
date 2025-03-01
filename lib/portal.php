<?php

define('MAX_COUNT_NOTACTIVATED_ACCS_ON_IP', 30);
define('MAX_TRIES_ACTIVATE', 10);

class portal
{

    public static $cur;
    public static mysqli $connection;
    public static $not_permitted_domains = ['universal', 'admin', 'portal', 'api'];
    static array $api_methods = [
        'portal' => ['check_domain_to_add', 'domain_is_active', 'register', 'activate', 'resend', 'test_create', 'get_list_owner_crm', 'delete_user', 'config','get_unactivated_crm'],
        'session' => ['regen'],
        'user' => ['auth', 'add'],
        'owner' => ['get_list', 'change', 'get_users_of_crm', 'get_email_logs', 'get_push_logs'],
        'user_of_owner' => ['change', 'get'],
        'extension' =>['add_to_crm', 'remove_from_crm', 'get_list', 'add_extension']
    ];
    static public $lang = "ru";

    public static function config($param)
    {
        try {
            logger::add(self::get_answer(null, 'invite', $param['user_country'] ? $param['user_country'] : 'ru'));
            return 0;
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
    private static function check_subdomain_format(string &$sub_domain)
    {
        $sub_domain = preg_replace('/\.laxo\.one$/', '', $sub_domain);
        $sub_domain = preg_replace('/b(laxo|one|laxo.|.laxo|one.|.one)b/', '', $sub_domain);

        $res = isset($sub_domain[2]) && preg_match('/^[a-z0-9-]+$/', $sub_domain);
        return $res;
    }

    static function check_domain_exist($sub_domain, $filter = '')
    {
        $sql = 'SELECT 1 FROM user WHERE user_domain=' . db_safe($sub_domain, DB_SAFE_QUOTAS) . ' ' . $filter . ' LIMIT 1';

        $res = db($sql);
        $res = mysqli_fetch_row($res);
        
        return (bool) $res;
    }

    static function domain_is_active($sub_domain)
    {
        if (is_null($sub_domain['country'])) {
            $sub_domain['country'] = 'ru';
        }
        self::$lang = $sub_domain['country'];
       
        
        $sub_domain = $sub_domain['user_domain'];
        $res = self::check_subdomain_format($sub_domain);
        if ($res) {
            return true;
            $res = self::check_domain_exist($sub_domain, ' AND user_activation_str IS NOT NULL');

            if (!$res) {
                evaErrorCode(-1);
                logger::add(self::get_answer(null, 'domain_not_found', self::$lang));


            } else {
                return true;
            }
        } else {
            evaErrorCode(-1);
            logger::add(self::get_answer(null, 'domain_is_not_valid', self::$lang));

        }
    }

    public static function check_domain_to_add(string $sub_domain): bool
    {

        if (in_array($sub_domain, self::$not_permitted_domains)) {
            evaErrorCode(-1);
            logger::add(self::get_answer(null, 'domain_permitted', self::$lang));
            return false;
        }
        $sub_domain = trim($sub_domain);
        $sub_domain = strtolower($sub_domain);

        $res = self::check_subdomain_format($sub_domain);
        if ($res) {
            $res = !self::check_domain_exist($sub_domain);

            if (!$res) {
                logger::add(self::get_answer(null, 'domain_exist', self::$lang));
                evaErrorCode(-1);
            }
        } else {
            evaErrorCode(-1);
            logger::add(self::get_answer(null, 'domain_is_not_valid', self::$lang));
        }
        return $res;

    }

    public static function register($param)
    {
        $uid = 0;
        $param['user_domain'] = mb_strtolower($param['user_domain']);
        $param['user_domain'] = trim($param['user_domain']);
        if (is_null($param['user_country'])) {
            $param['user_country'] = 'ru';
        }
        self::$lang = $param['user_country'];

        if (!self::check_domain_to_add($param['user_domain'])) {
            return $uid;
        }
        if (!filter_var($param['user_email'], FILTER_VALIDATE_EMAIL)) {
            evaErrorCode(-1);
            logger::add(self::get_answer(null, 'email', self::$lang));
            return $uid;
        }




        $param['user_activation_str'] = get_random_email_token();
        $param['user_register_date'] = time();
        $param['user_ip_create'] = ip();

        $sql_check = 'SELECT COUNT(*) FROM user WHERE  user_activation_str IS NOT NULL AND user_ip_create=' . db_safe($param['user_ip_create'], DB_SAFE_QUOTAS);

        $res_count = mysqli_fetch_row(db($sql_check))[0];

        if ($res_count < MAX_COUNT_NOTACTIVATED_ACCS_ON_IP) {
            $param['user_domain'] = preg_replace('/\.laxo\.one$/', '', $param['user_domain']);
            $fields = [
                'user_email',
                'user_name',
                'user_register_date',
                'user_born_date',
                'user_country',
                'user_source',
                'user_domain',
                'user_company_id',
                'user_activation_str',
                'user_ip_create',
                'user_phone'
            ];

            $req_fields = [
                'user_email',
                'user_name',
                'user_domain',
                'user_phone'
            ];

            if (isset($param['company_name']) && (strlen($param['company_name']) > 0)) {
                $company_id = addGenerator('company', ['company_name' => $param['company_name']], ['company_name']);
                $param['user_company_id'] = $company_id;
            }
            if (is_null($param['user_country'])) {
                // will be eng?
                $param['user_country'] = 'ru';
            }

            $param['user_phone'] = preg_replace('![^0-9]+!', '', $param['user_phone']);

            $uid = addGenerator('user', $param, $fields, $req_fields);


            if ($uid || evaErrorCodeFalse(-1)) {
                if ($param['user_password']) {
                    object_save((string) $param['user_password'], 'user_password_' . $uid);
                }

                dashaMail::mail_token($param['user_email'], $param['user_activation_str'], $param['user_domain']);
                sendpulse::add_contact_to_book([$param]);
                $data = [
                    'emails' => [['email' => $param['user_email'], 'variables' => ['Имя' => $param['user_name'], 'Phone' => $param['user_phone'], 'domain' => $param['user_domain'], 'дата регистрации' => date("d.m.y")]]]
                ];
                $url = SENDPULSE_URL . 'addressbooks/' . SENDPULSE_BOOK_ID . '/emails';
                sendpulse::query($data, $url);
                $uid = self::get_answer(null, 'success', self::$lang);
                $uid = str_replace("{user_email}", $param['user_email'], $uid);
            } else {
                evaErrorCode(-1);
                logger::add(self::get_answer(null, 'error', self::$lang));
            }
        } else {
            sleep(1);
            evaErrorCode(-1);
            logger::add(self::get_answer(null, 'limit_ip', self::$lang));
        }

        return $uid;
    }

    public static function resend(string $email)
    {
        $res = false;
        $param['user_email'] = $email;
        if (filter_var($param['user_email'], FILTER_VALIDATE_EMAIL)) {
            $param['user_activation_str'] = get_random_email_token();
            $cond = ' WHERE user_email=' . db_safe($param['user_email'], DB_SAFE_QUOTAS) . ' AND user_activation_str IS NOT NULL LIMIT 1';
            $sql = 'UPDATE user SET user_activation_str = ' . $param['user_activation_str'] . $cond;
            sleep(10);
            db($sql);
            if (affected_rows()) {
                $res = mail_token($param['user_email'], $param['user_activation_str']);
            }
        }

        return $res;
    }

    public static function activate($param)
    {
        $obj_name = 'cache/email_try_' . md5($param['user_email']);
        $tries = object_exist($obj_name) ? object_get($obj_name) : 0;

        $ret = false;
        if ($tries < MAX_TRIES_ACTIVATE) {

            $cond = ' WHERE user_email=' . db_safe($param['user_email'], DB_SAFE_QUOTAS) .
                ' AND user_activation_str=' . db_safe($param['user_activation_str'], DB_SAFE_QUOTAS) . ' LIMIT 1';

            $join = ' LEFT JOIN company ON company.company_id = user.user_company_id ';
            $sql = 'SELECT user_id,user_domain,user_country, user_name  as "contact_name", user_phone, company.company_name as user_company  FROM user ' . $join . $cond;
            sleep(1);
            $res = mysqli_fetch_assoc(db($sql));

            if ($res) {
                error_reporting(E_ALL);
                $sql = 'UPDATE user SET user_activation_str = NULL' . $cond;

                if ($tries) {
                    object_remove($obj_name);
                }



                db($sql);
                if (affected_rows()) {

                    $pass_obj_name = 'user_password_' . $res['user_id'];
                    if (object_exist($pass_obj_name)) {
                        $res['user_password'] = object_get($pass_obj_name);
                        object_remove($pass_obj_name);
                    }

                    $res['user_login'] = $res['user_email'] = $param['user_email'];

                    $ret = self::create($res);
                }
            } else {
                sleep(5);
                object_save(++$tries, $obj_name);
               logger::add(self::get_answer(null, 'system_already_init', self::$lang));

            }
        }

        return $ret;
    }

    static private function set_tarif(int $id, int $uid, int $duration, int $start = 0): object
    {
        $ret = null;
        if (!$start) {
            $start = time();
        }
        $end = $duration + $start;

        $param = [
            'tarif_id' => $id,
            'user_id' => $uid,
            'date_activate' => $start,
            'date_need_payed' => $end
        ];

        $req_fields = $fields = array_keys($param);

        $tid = addGenerator('tarif_history', $param, $fields, $req_fields);

        if ($tid) {
            $sql = 'SELECT date_activate,date_deactivate,date_need_payed,tarif.tarif_id,tarif_person_count,tarif_price,tarif_description FROM tarif_history INNER JOIN tarif ON tarif_history_id=' . $tid . ' AND tarif.tarif_id=tarif_history.tarif_id LIMIT 1';
            $res = db($sql);
            if ($res) {
                $ret = mysqli_fetch_object($res);
            }
        }

        return $ret;
    }

    static function _query($class, $method, $data, $sid = false)
    {
        $url = 'https://devserver_api.com/';
        $domain = basename($data['crm_name'], 'dbError');
        $postdata = [];
        $postdata[0]['class'] = $class;
        $postdata[0]['method'] = $method;
        $postdata[0]['param'] = $data;
        if (!$sid) {
            $sql = 'SELECT LOWER(HEX(session.session_id)) as sid FROM user INNER JOIN session USING(user_id) WHERE user.user_domain=' . db_safe($domain, DB_SAFE_QUOTAS);
            $sid = db_run_row($sql)['sid'];
            $postdata[0]['param']['token'] = $sid;
        } elseif ($sid) {
            $postdata[0]['sid'] = $sid;
        }


        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json' . PHP_EOL .
                    'Origin: https://' . $domain . 'dbError' . PHP_EOL,
                'content' => http_build_query($postdata)
            ]
        ]);

        try {

            $result = file_get_contents($url, false, $context);
            file_put_contents('/home/maxim/repos/portal/www/context.json',  json_encode($postdata) . "\n", FILE_APPEND);
        } catch (Throwable $e) {
            logger::put('Fail to query API:' . $url);
        }
        return $result;
    }
    static function query($class, $method, $data, $sid = false)
    {
        $url = 'https://devserver_api.com/';
        $domain = basename($data['crm_name'], 'dbError');
        $postdata = [];
        $postdata[0]['class'] = $class;
        $postdata[0]['method'] = $method;
        $postdata[0]['param'] = $data;
        if (!$sid) {
            $sql = 'SELECT LOWER(HEX(session.session_id)) as sid FROM user INNER JOIN session USING(user_id) WHERE user.user_domain=' . db_safe($domain, DB_SAFE_QUOTAS);
            $sid = db_run_row($sql)['sid'];
            $postdata[0]['param']['token'] = $sid;
        } elseif ($sid) {
            $postdata[0]['sid'] = $sid;
        }



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL =>  'https://devserver_api.com/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
                'Accept: */*',
                'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                'Connection: keep-alive',
                'Content-Type: text/plain;charset=UTF-8',
                'Origin: https://' . $domain . 'dbError',
            ),
        ));


        try {

            $response = curl_exec($curl);
            curl_close($curl);
            file_put_contents('/response.json',  json_encode($response) . "\n", FILE_APPEND);
        } catch (Throwable $e) {
            logger::put('Fail to query API:' . $url);
        }
        return $response;
    }


    static public function drop(string $domain)
    {
        system('rm -R  ' . APIROOT . 'env/' . $domain . '/');
        db('drop database Client2_' . $domain . ';');
        db('DROP USER Client2_' . $domain . '@localhost;');
        db('UPDATE user SET user_activation_str =100 WHERE user_domain="' . $domain . '"');
    }

    static public function test_create($param)
    {

        $domain = 'cascade';
        self::drop($domain);

        $param['user_id'] = 164;
        $param['user_domain'] = $domain;
        return self::create($param);
    }

    static public function create($param)
    {

        $domain = $param['user_domain'];
        $domain_root = APIROOT . 'env/' . $domain . '/';

        if (!file_exists($domain_root)) {
            $dir_list = ['', 'daemons', 'files', 'logs', 'cache', 'obj', 'import'];
            foreach ($dir_list as $dir) {
                mkdir($domain_root . $dir);
            }

            // add lang
            $lang = (array)file_get_contents('default/user_data');
            $lang['lang'] = $param['user_country'];
            file_put_contents('default/user_data', json_encode($lang));

            copy(ROOT . 'default/user_data', $domain_root . 'obj/default');
            copy(ROOT . 'default/integration', $domain_root . 'obj/integration');
            $props = new stdClass();

            $props->version_id = 5;
            $props->tarif = self::set_tarif(FREE_TARIF_ID, $param['user_id'], FREE_TIME);



            if (strpos($domain, '-') !== false) {
                $domain = str_replace('-', '_', $domain);
            }

            $props->db = (object) [
                'name' => 'Client2_' . $domain,
                'user' => 'Client2_' . $domain,
                'host' => 'localhost',
                'pass' => get_random_token()
            ];

            $props->modules = [];

            $init_props = [];
            $init_props['init'] = $param;
            $init_props['init']['sid'] = session::init($param['user_id']);
            $props->sid = $init_props['init']['sid'];
            $props->user_id = session::$user_id;
            $props->portal_sid = session::$portal_sid;
            object_save($props, 'instanse.config.json', $domain_root);
            object_save($init_props, 'migrate.json', $domain_root . 'obj/');

            $commands = 'create database ' . $props->db->name  . ';
                CREATE SCHEMA IF NOT EXISTS ' . $props->db->name  . ';
                USE ' . $props->db->name  . ';
                CREATE USER "' . $props->db->user . '"@"' . $props->db->host . '" IDENTIFIED BY "' . $props->db->pass . '";
                    GRANT ALL PRIVILEGES ON ' . $props->db->name . '.* TO "' . $props->db->user . '"@"' . $props->db->host . '";
                    FLUSH PRIVILEGES;';

            $command_arr = explode(';', $commands);

            foreach ($command_arr as $command) {
                if ($command)
                    self::$connection->query($command);
            }



            $remote_connection = mysqli_connect($props->db->host, $props->db->user, $props->db->pass, $props->db->name);

            $commands = file_get_contents(ROOT . 'db_create/db.sql');

            $command_arr = explode(';', $commands);

            foreach ($command_arr as $command) {
                try {
                    $remote_connection->query($command);
                } catch (Throwable $e) {
                    file_put_contents('errors_create.txt', $command . PHP_EOL);
                }
            }


            $commands = file_get_contents(ROOT . 'db_create/inserts.sql');

            $command_arr = explode(';', $commands);

            foreach ($command_arr as $command) {
                if ($command) {
                    try {
                        $remote_connection->query($command);
                    } catch (Throwable $e) {
                        file_put_contents('errors_insert.txt', $command . PHP_EOL);
                    }
                }
            }
            sleep(3);

        }


        return true;
    }

    static function init($props)
    {
        self::$cur = $props;

        if (!property_exists(self::$cur, 'db')) {
            return evaErrorCodeFalse(501);
        }

        $db = self::$cur->db;

        self::$connection = mysqli_connect($db->host, $db->user, $db->pass, $db->name);

        unset(self::$cur->db);
    }

    static function check_code_invite(string $code = "")
    {
        $result = false;
        $sql = "SELECT 1 FROM `code_invite` WHERE code_invite_value='{$code}'";
        $ret = db_run($sql);
        if ($ret) {
            $result = true;
        }
        return $result;
    }

    static function delete_user($param)
    {
        $id = $param['user_id'];
        $domain = db_safe($param['user_domain']);
        $sql = 'DELETE FROM session WHERE user_id = ' . db_safe((int)$id);
        db($sql);
        $sql = 'DELETE FROM tarif_history WHERE user_id = ' . db_safe((int)$id);
        db($sql);
        $sql = 'DELETE FROM user WHERE user_id = ' . db_safe((int)$id);
        db($sql);
        self::drop($domain);
    }

    static function stop_proccess_by_domain(string $domain = '')
    {
        $shell_arg_php = '$_SERVER[\'HTTP_ORIGIN\'] =\'' . $domain . '\';require_once(\'' . APIROOT . 'init.php\');stop_default_proccess();';
        $shell_arg_php = escapeshellarg($shell_arg_php);

        $shell_arg_bash = '/bin/php8.1 -r ' . $shell_arg_php . ' &> ' . APIROOT . 'logs/email_getter.txt < /dev/null & ';

        $shell_arg_bash = '/bin/nohup ' . $shell_arg_bash;

        $shell_arg_bash = escapeshellarg($shell_arg_bash);
        $cmd = '/bin/bash -c ' . $shell_arg_bash;
        system($cmd);
    }

    static public function get_answer(string|null $str = null, string $status = 'success', string $lang = 'ru')
    {

        $answer = (array) object_get('lang.json', WEBROOT);
        $langs = array_keys($answer);
        if (!in_array($lang, $langs)) {
            $lang = 'ru';
        }

        return $answer[$lang]->$status;
    }
    static public function get_unactivated_crm(){
        $time_expired  = (30 * 24 * 60 * 60);
        $only_one = ' ';
        $result = owner::get_list(false, true, ' WHERE  user_activation_str IS NOT NULL AND user_register_date < ' . (time() - $time_expired) . $only_one);
        $res = [];
        foreach ($result as &$row) {
            if (empty($row['date_activate']) && (time() - $row['user_register_date']) > $time_expired) {
                $row['user_register_date'] =  date('d.m.Y', $row['user_register_date']);
                $res[] = $row;
            }
        }
        return $res;
    }

    static public function delete_crm_by_time_expired($admin = false)
    {
        if ($admin) {
            $params = self::get_unactivated_crm();
            foreach ($params as $param) {
                try {
                    self::delete_user($param);
                } catch (Throwable $e) {
                }
            }
        }
    }
}
