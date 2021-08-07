<?php

use Engine\Loader;
use Engine\Request;
use Engine\Response;
use Engine\Router;
use Error\Base;
use Library\DB;
use Library\Template;
use Library\User;

// PHP init
date_default_timezone_set('UTC');
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
spl_autoload_register(function($name) {
    preg_match(
        "/^(?:(?'namespace'(?'type'[^\\\\]+)(?'path'(?:\\\\[^\\\\]+)*))\\\\)?(?'class'[^\\\\]+)$/",
        $name,
        $matches
    );
    $type = $matches['type'];
    $namespace = $matches['namespace'];
    $path = strtolower(str_replace('\\','/',$matches['path']));
    $class = $matches['class'];
    $full_class = $namespace.'\\'.$class;
    $file = "$path/$class.php";
    switch($type){
        case 'Controller':
            $file = DIR_CONTROLLER.$file;
            break;
        case 'Model':
            $file = DIR_MODEL.$file;
            break;
        case 'View':
            $file = DIR_VIEW.$file;
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
            $file = DIR_CLASSES.'/'.strtolower($type).$file;
    }
    if(!is_file($file))  throw new Exception("File '$file' not found, autoload class '$name'");
    include_once $file;
    if(!class_exists($full_class) and !interface_exists($full_class)) throw new Exception("Class $full_class not exists, autoload class '$name'");
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
    //User::tokenAuth($_COOKIE['access_token']);

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

    Router::map('/','Home::home',['GET']);
    //Router::map('/login','User::login',['GET']);
    Router::map('/service/:id','Service::page',['GET'],['id'=>'[1-9][0-9]*']);
    Router::map('/services','Service::list',['GET']);
    Router::map('/service/add','Service::add',['GET','POST']);
    Router::map('/service/:id/edit','Service::edit',['GET'],['id'=>'[1-9][0-9]*']);
    Router::map('/service/:id/delete','Service::delete',['GET'],['id'=>'[1-9][0-9]*']);

    Router::map('/countries','Country::list',['GET']);
    Router::map('/country/add','Country::add',['GET','POST']);
    Router::map('/country/:id/delete','Country::delete',['GET'],['id'=>'[1-9][0-9]*']);

    Template::init(DIR_TEMPLATE);
    Template::setTitleTemplate('%s | SMS','SMS');

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

