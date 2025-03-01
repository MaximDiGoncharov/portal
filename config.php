<?php

/*
  THIS CONFIG is used for code defined constants. To properties of solution use  instanse.config.json
 */


define('APIROOT', '/');
define('WPROOT', '/wp/');
define('ROOT', __DIR__ . '/');
define('EXTENSION_DIR', APIROOT.'extension/');
define('OBJ_ROOT', ROOT);
define('WEBROOT', ROOT . '/www/');
define('FREE_TIME', 3600 * 24 * 60); // 60
define('FREE_TARIF_ID', 1);
define('FROM', 'bot@admin.com');
define('API_USER_ID', '123456789');
define('API_SECRET', '123456789');
define('SENDPULSE_URL', 'https://api.sendpulse.com/');
define('SENDPULSE_BOOK_ID', 111);
define('ID_TEMPLATE_MAIL_TOKEN', 112);
define('ID_TEMPLATE_ERROR', 113);
define('ID_TEMPLATE_SENDPUlSE', 114);
define('CRM_SERVER_INIT', true);
define('IMAP_PASSWORD', '123');
define('IMAP_URI', '{imap.mail.ru:993/imap/ssl}INBOX');
define('IMAP_LOGIN', FROM);
define('RECIEVE_DELAY', 10);
define('DASHA_TEMPLATE_MAIL', ROOT . 'template/mail/');
define('DASHA_API_KEY',  '123456789');
define('DASHA_URL_TRANSACTION',  "https://api.dashamail.com/");
define('EXTENSION_ROOT', __DIR__ . '/extension/');
