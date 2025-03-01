<?php

class extension
{
    public static $file = EXTENSION_ROOT . 'extension.json';
    public  static function check_extension($name)
    {
        $file_path = EXTENSION_ROOT  . $name;
        if (file_exists($file_path)) {
            return true;
        }
        return false;
    }

    public static function get_list(array $param = [])
    {

        $data = file_get_contents(self::$file);
        $data = (array) json_decode($data);
        if (isset($param['category'])) {
            return $data['category'];
        }
        return $data['extension'];
    }

    public static function add_to_crm(array $param)
    {
        $extension_name = $param['extension_name'];
        $extension_list = (array) self::get_list();

        if (!isset($param['crm_name'])) {
            logger::add('Crm not found');
            return false;
        }


        if (isset($extension_list[$extension_name]) && self::check_extension($extension_name)) {
            $config_path = APIROOT . 'env/' . $param['crm_name'] . '/obj/extension.json';
            $config = file_get_contents($config_path);
            $config = (array) json_decode($config);
            $new_obj = ['is_access' => true, 'is_active' => false, 'date_create' => time(), 'date_expired' => (time() + (60 * 60 * 24 * 30))];
            $new_obj = array_merge($new_obj, (array) $extension_list[$extension_name]);

            $config[$extension_name] = $new_obj;
            file_put_contents($config_path, json_encode($config));
            return true;
        } else {
            logger::add('Extension not found');
            return false;
        }
    }

    public static function remove_from_crm(array $param)
    {
        if (!isset($param['crm_name']) || !isset($param['extension_name'])) {
            logger::add('Crm or extension not found');
            return false;
        } else {
            $extension_name = $param['extension_name'];
            $config_path = APIROOT . 'env/' . $param['crm_name'] . '/obj/extension.json';
            $config = file_get_contents($config_path);
            $config = (array) json_decode($config);


            unset($config[$extension_name]);
            file_put_contents($config_path, json_encode($config));
        }
    }


    public static function add_extension(array $param = [])
    {

        $obj = ['view_name' => $param['view_name'], 'category' => $param['category'], 'icon' => $param['icon'], 'price' => $param['price'], 'tags' => $param['tags'], 'is_server' => $param['is_server']];
        $data = file_get_contents(self::$file);
        $data = (array) json_decode($data);
        $data['extension'] = (array)  $data['extension'];

        $data['extension'][$param['name']] =  $obj;
        file_put_contents(self::$file, json_encode($data));
        return true;
    }
}
