<?php namespace Controller;

use Engine\IController;
use Engine\Request;
use Engine\Response;
use Model\Country as Model_Country;
use View\PageAddCountry;

class Country implements IController {

    public static function list(){
        $countries = Model_Country::getAllCountries();

        Response::setOutput(\View\PageCountries::render($countries));
    }

    public static function add(){
        $method = Request::$server['REQUEST_METHOD'];

        if($method=='GET'){
            Response::setOutput(PageAddCountry::render());
        }elseif($method=='POST'){
            $name = Request::$post['name'];

            if(empty($name)) throw new \Error\Request();

            Model_Country::addCountry($name);
            Response::redirect('/countries');
        }
    }

    public static function delete($param = []){
        $country_id = (int)($param['id']??0);
        Model_Country::deleteCountry4id($country_id);
        Response::redirect('/countries');
    }


}