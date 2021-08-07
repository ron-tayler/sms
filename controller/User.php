<?php namespace Controller;

use Engine\IController;
use Engine\Response;
use View\Login as View_Login;

class User implements IController{

    static function login(){
        Response::setOutput(View_Login::render());
    }
}