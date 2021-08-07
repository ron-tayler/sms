<?php

namespace Engine;
use ExceptionBase;

/**
 * Class Loader
 * @package Engine
 * @author Ron_Tayler
 * @copyright 04.05.2021
 */
class Loader {

    /**
     * Loader constructor.
     */
    private function __construct(){}

    /**
     * Method controller - Загрузка контролеров
     * @param string $name
     * @throws ExceptionBase
     */
    public static function controller(string $name){
        self::load($name,DIR_CONTROLLER);
        $class = 'Controller\\'.str_replace('/','\\',$name);
        if(!class_exists($class)) throw new ExceptionBase('Класс '.$class.' Не удалось объявить',5);
    }

    /**
     * Method model - Загрузка моделей
     * @param string $name
     * @throws ExceptionBase
     */
    public static function model(string $name){
        self::load($name,DIR_MODEL);
        $class = 'Model\\'.str_replace('/','\\',$name);
        if(!class_exists($class)) throw new ExceptionBase('Класс '.$class.' Не удалось объявить',5);
        if(!is_callable(Array($class, "init"))) throw new ExceptionBase('Невозможно вызвать метод '.$class.'::init',5);
        call_user_func(Array($class, "init"));
    }

    /**
     * Method library - Загрузка моделей
     * @param string $name
     * @throws ExceptionBase
     */
    public static function library(string $name){
        self::load($name,DIR_LIB);
    }

    /**
     * Method load - Универсальный загрузчик
     * @param string $name 'Video' or 'User/Chanel/Video'
     * @param string $dir DIR_...
     * @throws ExceptionBase
     */
    private static function load(string $name,string $dir){
        $reg = /** @lang PhpRegExp */ '/^(?:[A-Za-z_]+)(?:\/[A-Za-z_]+)*$/';
        if(preg_match($reg,$name)!==1){
            throw new ExceptionBase("Name '$name' указанно неверно",5);
        }
        $file = $dir.'/'.strtolower($name).'.php';
        if(!is_file($file)) throw new ExceptionBase("File '$file' не найден",5);
        require_once $file;
    }
}