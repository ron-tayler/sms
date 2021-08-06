<?php namespace Controller;

use Engine\IController;
use Engine\Response;

class Test implements IController
{

    static function init()
    {
        // TODO: Implement init() method.
    }

    static function index(array $param = [])
    {
        // TODO: Implement index() method.
    }

    static function test(){
        Response::setOutput('Hello World!');
    }
}