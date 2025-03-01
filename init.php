<?php

require 'config.php';

$req = glob(__DIR__ . '/lib/*.php');

foreach ($req as $r) {
    require_once $r;
}

unset($req);

$props = object_get('instanse.config.json');
Portal::init($props);
