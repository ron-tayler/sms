<?php

// Проверка конфигов из config.php
foreach ( [
    'DISPLAY_ERROR',
    'LOGFILE_ERROR',
    'DEBUG_TOKEN',
    'HTTP_URL',
    'HTTPS_URL',
    'SITE_URL',
    'DIR_APP',
    'DB_HOST',
    'DB_PORT',
    'DB_USER',
    'DB_PASS',
    'DB_NAME'
] as $name){
    if(!defined($name)) trigger_error(
        "Конфигурационная константа $name не указанна в config.php",
        E_USER_ERROR
    );
}

// DIR / Директории
define('DIR_SYSTEM',        DIR_APP.'/system');
define('DIR_CONTROLLER',    DIR_APP.'/controller');
define('DIR_MODEL',         DIR_APP.'/model');
define('DIR_LANGUAGE',      DIR_APP.'/language');
define('DIR_VIEW',          DIR_APP.'/view');
define('DIR_STORAGE',       DIR_APP.'/storage');
define('DIR_LIB',           DIR_APP.'/lib');

define('DIR_ENGINE',        DIR_SYSTEM.'/engine');

define('DIR_TEMPLATE',      DIR_VIEW.'/template');
define('DIR_JS',            DIR_VIEW.'/js');
define('DIR_CSS',           DIR_VIEW.'/css');

define('DIR_IMAGE',         DIR_STORAGE.'/image');
define('DIR_FILE',          DIR_STORAGE.'/file');
define('DIR_VIDEO',         DIR_STORAGE.'/video');
define('DIR_UPLOAD',        DIR_STORAGE . '/upload');
define('DIR_DOWNLOAD',      DIR_STORAGE . '/download');
define('DIR_LOGS',          DIR_STORAGE . '/logs');
define('DIR_SESSION',       DIR_STORAGE . '/session');
define('DIR_CACHE',         DIR_STORAGE.'/cache');

define('DIR_CACHE_FILE',    DIR_CACHE.'/file');
define('DIR_CACHE_IMAGE',   DIR_CACHE.'/img');

// Debug
define('DEBUG_DUMP',1);
define('DEBUG_GET',1<<1);
define('DEBUG_POST',1<<2);
define('DEBUG_PRIVATE',1<<3);
$DEBUG_CODES = [
    'dump'=>DEBUG_DUMP,
    'get'=>DEBUG_GET,
    'post'=>DEBUG_POST,
    'private'=>DEBUG_PRIVATE
];
$DEBUG_CODES['all'] = 0;
foreach($DEBUG_CODES as $debug_code){
    $DEBUG_CODES['all'] |= $debug_code;
}
define('DEBUG_CODES',$DEBUG_CODES);
unset($DEBUG_CODES,$DEBUG_CODE_ALL,$debug_code);