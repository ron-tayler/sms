<?php

use Engine\Loader;
use Engine\Request;
use Engine\Response;
use Engine\Router;
use Error\Base;
use Library\DB;

// PHP init
error_reporting(E_ALL);
$time = explode(" ", microtime());
define('TIME_SEC',$time[1]);
define('TIME_USEC',$time[0]);
unset($time);

require_once './config.php';
require_once './core_config.php';

// Debug
$debug_text = ($_GET['debug_token']??'' == DEBUG_TOKEN) ? ($_GET['debug']??'') : '';
foreach (explode(',', $debug_text) as $el) $debug_type = ($debug_type ?? 0) | (DEBUG_CODES[$el] ?? 0);
define('DEBUG_CODE', $debug_type ?? 0);
unset($debug_type, $debug_text);

// Автозагрузка классов
spl_autoload_extensions('.php');
spl_autoload_register(function($class) {
    preg_match(
        "/^(?:(?'namespace'(?'type'[^\\\\]+)(?'path'(?:\\\\[^\\\\]+)*))\\\\)?(?'class'[^\\\\]+)$/",
        $class,
        $matches
    );
    $type = $matches['type'];
    $namespace = $matches['namespace'];
    $path = strtolower(str_replace('\\','/',$matches['path']));
    $class = strtolower($matches['class']);
    $full_class = $namespace.'\\'.$class;
    $file = "$path/$class.php";
    switch($type){
        case 'Controller':
            $file = DIR_CONTROLLER.$file;
            break;
        case 'Model':
            $file = DIR_MODEL.$file;
            break;
        case 'Error':
            $file = DIR_SYSTEM.'/Error'.$file;
            break;
        case 'Exception':
            $file = DIR_SYSTEM.'/Exception'.$file;
            break;
        case 'Library':
            $file = DIR_LIB.$file;
            break;
        case 'Engine':
            $file = DIR_ENGINE.$file;
            break;
        default:
            $file = DIR_APP.'/'.strtolower($type).$file;
    }
    if(!is_file($file))  throw new Exception("File '$file' not found");
    include_once $file;
    if(!class_exists($full_class)) throw new Exception("Class $full_class not exists");
});

try{
    // Логирование ошибок
    Engine\Log::init('debug',DIR_LOGS);
    $log = Engine\Log::init('error',DIR_LOGS);
    set_error_handler(function ($code, $message, $file, $line) use ($log) {
        // error suppressed with @
        if (error_reporting() === 0) {
            return false;
        }
        switch ($code) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $error = 'Warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                break;
            default:
                $error = 'Unknown';
                break;
        }
        if (DISPLAY_ERROR) {
            echo PHP_EOL.'<b>' . $error . '</b>: ' . $message . ' in <b>' . $file . '</b> on line <b>' . $line . '</b>';
        }
        if (LOGFILE_ERROR) {
            $log->print('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);
        }
        return true;
    });

    // Composer autoload.php
    require_once DIR_APP.'/vendor/autoload.php';

    Request::init();

    // Инициализация Базы Данных
    Loader::library('DB');
    DB::init('base',[
        'adaptor'=>'mysqli',
        'hostname'=>DB_HOST,
        'port'=>DB_PORT,
        'username'=>DB_USER,
        'password'=>DB_PASS,
        'database'=>DB_NAME
    ]);

    Router::map('','Test::test',['GET']);

    Router::execute();

    echo Response::getOutput();

}catch(Base $err){
    $error_response = [
        'code'=>$err->getCode(),
        'message'=>$err->getMessage()
    ];
    if(DEBUG_CODE & DEBUG_PRIVATE) $error_response['private'] = $err->getPrivateMessage();
    echo json_encode(['error'=>$error_response],JSON_UNESCAPED_UNICODE);
}

