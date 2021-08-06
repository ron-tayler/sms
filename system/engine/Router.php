<?php namespace Engine;

use Error\Engine;

/**
 * Class Router - Маршрутизатор запросов
 * @package Engine
 * @author Ron_Tayler
 * @copyright 04.05.2021
 */
final class Router {
    /** @var Route[] */
    private static array $routes;
    private static Route $baseRoute;
    private static Route $errorRoute;

    private function __construct(){}

    public static function setBase(string $target){
        self::$baseRoute = new Route('/',$target);
    }
    public static function setError(string $target){
        self::$errorRoute = new Route('/',$target);
    }

    public static function map(string $url, string $target, array $methods = [], array $filters = [], array $version = []){
        self::$routes[] = new Route($url,$target,$methods,$filters,$version);
    }

    public static function maps(array ...$routes){
        $map = function($namespace,$routes){
            foreach($routes as $route){
                if( isset($route['target']) ){
                    self::map($namespace.$route['path'],$route['target']);
                }
            }
        };
        $map('',$routes);
    }

    /**
     * Method execute - Запуск обработки маршрутизации
     * @param string $version
     * @throws
     */
    public static function execute($version = '1.0'){
        $version = explode('.',$version);
        $url = explode('?',Request::$server['REQUEST_URI'])[0];
        $executed = false;
        $error = null;
        foreach (self::$routes as $route){
            // Проверка версии
            $minVersion = explode('.',$route->getMinVersion());
            if($version[0]<$minVersion[0] OR $version[0]==$minVersion[0] AND $version[1]<$minVersion[1]) continue;
            if($route->getMaxVersion()!='') {
                $maxVersion = explode('.', $route->getMaxVersion());
                if ($version[0] > $maxVersion[0] OR $version[0]==$maxVersion[0] and $version[1] > $maxVersion[1]) continue;
            }

            // Проверка url по регулярному выражению
            $pat = str_replace('/','\\/',$route->getRegex());
            $res = preg_match('/^'.$pat.'$/',$url,$request_params);
            if($res!==1) continue;

            // Проверка отдельных параметров по регулярному выражению
            array_splice($request_params,0,1);
            $params = array_combine($route->getParams(),$request_params);
            foreach ($params as $name => $value){
                $res = preg_match('/'.$route->getFilters()[$name].'/',$value);
                if($res!==1) continue(2);
            }

            // Запуск контроллера
            try{
                $route->execute($params);
                $executed = true;
            }catch(\ExceptionBase $ex){
                trigger_error($ex->getPrivateMessage(),E_USER_NOTICE);
                $error = $ex;
                continue;
            }
            break;
        }
        if(!$executed){
            if($error){
                throw $error;
            }else{
                throw new Engine('Контроллер не найден: '.$url.' - v'.$version[0].'.'.$version[1],'Не найден метод API или версия не верна');
            }
        }
}

    private function checkingConvergence(){

    }
}
