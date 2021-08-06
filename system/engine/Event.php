<?php

namespace Engine;

/**
 * Class Event
 * @package Engine
 * @author Ron_Tayler
 * @copyright 04.05.2021
 */
class Event{

    /** @var Route[] */
    private static array $events = [];

    public static function add(string $hook, string $target){
        self::$events[] = new Route($hook,$target);
    }

    /**
     * @param string $hook
     * @param array $param
     * @throws \ExceptionBase
     */
    public static function exec(string $hook,array $param){
        foreach (self::$events as $route){
            if($route->getPattern()==$hook){
                $route->execute($param);
            }
        }
    }
}