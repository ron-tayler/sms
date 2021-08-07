<?php namespace Controller;

use Engine\IController;
use Engine\Request;
use Engine\Response;
use Model\Service;
use Model\Country;
use View\Home as View_Home;

class Home implements IController {

    public static function home(array $param = []){
        $country_id = (int)(Request::$get['country']??0);

        if($country_id>0){
            $services = Service::getServices4CountryId($country_id);
        }else{
            $services = Service::getAllService();
        }


        $cards = [];
        foreach ($services as $service){
            $card = [
                'id'=>$service->id,
                'name'=>$service->name,
                'country'=>Country::getCountry4id($service->country_id)->name,
                'count'=>$service->count,
                'price'=>$service->prices[array_key_last($service->prices)]->price
            ];
            $cards []= $card;
        }

        Response::setOutput(View_Home::render($cards,$country_id));
    }

}