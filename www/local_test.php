<?php

require '../init.php';




$name = 'example';
$res = extension::add_extension([
   "name" => "cola",
   "view_name" => 2,
   "category" => 3,
   "icon" => 4,
   "price" => 5,
   "tags" => [
      1,
      2
   ]
]);
var_dump($res);
die;
// dashaMail::mail_token('m.goncharov@laxo.work', 12345, 'example');
// dashaMail::mail_token('Maxpups111@yandex.ru', 12345, 'example');
// dashaMail::mail_token('maxpups101@gmail.com', 12345, 'example');

// $sql = 'SELECT * FROM `user` WHERE user_id = 501';
// $res = db_run_row($sql);
// var_dump($res);
// die;