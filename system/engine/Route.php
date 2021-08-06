<?php

namespace Engine;
use ExceptionBase;

/**
 * Class Route - Маршрут для маршрутизатора
 * @package YouInRoll.com
 * @author Ron_Tayler
 * @copyright 2021
 */
class Route{
    private string $pattern;
    private string $target;
    private array $methods = ['GET','POST','PUT','DELETE'];
    private array $filters = [];
    private array $params = [];
    private string $version_min = '1.0';
    private string $version_max = '';

    /**
     * Route constructor.
     * @param string $pattern Example: '/api/:method/:id/:token' or '/user/:id/chanel/getVideos'
     * @param string $target
     * @param array $methods
     * @param array $filters
     * @param array $version
     */
    public function __construct(string $pattern, string $target, array $methods = [], array $filters = [], array $version = []){
        $this->pattern = $pattern;
        $this->target = $target;
        $this->methods = $methods;
        $this->filters = $filters;
        preg_match_all('/:(\w+)/',$pattern,$params);
        $this->params = $params[1];
        $this->version_min = $version[0]??'1.0';
        $this->version_max = $version[1]??'';
    }

    /**
     * @param array $params
     * @throws ExceptionBase
     */
    public function execute(array $params){
        // Первая группа полное имя Класса, Вторая группа Method
        $reg = /** @lang PhpRegExp */ '/^((?:[A-Z][A-Za-z_]*)+(?:\/[A-Z][A-Za-z_]*)*)(?:::([a-z][A-Za-z_]+))?$/x';
        if(preg_match($reg,$this->target,$match, PREG_UNMATCHED_AS_NULL)===1){
            $controller = $match[1];
            $method = $match[2]??'index';
        }else{
            throw new ExceptionBase("Target \'$this->target\' объявлен не правильно",5);
        }

        Loader::controller($controller);

        $class = 'Controller\\'.str_replace('/','\\',$controller);
        if(!is_callable(array($class, $method))) throw new ExceptionBase('Невозможно вызвать метод '.$class.'::'.$method,5);

        call_user_func(Array($class, $method),$params);
    }

    public function getPattern(){
        return $this->pattern;
    }
    public function getTarget(){
        return $this->target;
    }
    public function getMethods(){
        return $this->methods;
    }
    public function getFilters(){
        return $this->filters;
    }
    public function getParams(){
        return $this->params;
    }
    public function getMinVersion(){
        return $this->version_min;
    }
    public function getMaxVersion(){
        return $this->version_max;
    }
    public function getRegex(){
        return preg_replace_callback('/:(\w+)/', array(&$this, 'substituteFilter'), $this->pattern);
    }
    private function substituteFilter($matches) {
        if (isset($matches[1]) && isset($this->filters[$matches[1]])) {
            return '('.$this->filters[$matches[1]].')';
        }
        return "([\d\w-]+)";
    }
}