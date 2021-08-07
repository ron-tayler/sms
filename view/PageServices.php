<?php namespace View;

use Engine\IView;
use Library\Template;
use Model\Country;

class PageServices implements IView {

    static function render($services = []): string{
        $view_services = [];
        foreach ($services as $service){
            $view_services []= [
                'id'=>$service->id,
                'name'=>$service->name,
                'country'=>Country::getCountry4id($service->country_id)->name,
                'count'=>$service->count,
                'price'=>$service->prices[array_key_last($service->prices)]->price
            ];
        }
        return Base::render(Template::render('/page_services.twig',[
            'services'=>$view_services
        ]));
    }
}