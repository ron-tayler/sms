<?php

define('DIR_SITE',__DIR__);
define('DIR_SYSTEM',DIR_SITE.'/system');
define('DIR_CONTROLLER',DIR_SITE.'/controller');
define('DIR_LIB',DIR_SITE.'/lib');
define('DIR_MODEL',DIR_SITE.'/model');
define('DIR_VIEW',DIR_SITE.'/view');
define('DIR_LOGS',DIR_SITE.'/logs');

define('DEBUG_DUMP',1);
define('DEBUG_GET',1<<1);
define('DEBUG_POST',1<<2);
$DEBUG_CODES = [
    'dump'=>DEBUG_DUMP,
    'get'=>DEBUG_GET,
    'post'=>DEBUG_POST
];

$DEBUG_CODES['all'] = 0;
foreach($DEBUG_CODES as $debug_code){
    $DEBUG_CODES['all'] |= $debug_code;
}

define('DEBUG_CODES',$DEBUG_CODES);
unset($DEBUG_CODES,$DEBUG_CODE_ALL,$debug_code);