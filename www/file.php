<?php

require '../init.php';
if (function_exists('file_route')) {
    file_route();
} else {
    require 'log.php';
}