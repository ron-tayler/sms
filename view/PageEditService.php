<?php namespace View;

use Engine\IView;
use Library\Template;
use Model\Country;
use Service;

class PageEditService implements IView {

    static function render(Service $service = null): string{
        return Base::render(Template::render('page_edit_service.twig',[
            'id'=>$service->id,
            'name'=>$service->name,
            'country'=> Country::getCountry4id($service->country_id)->name,
            'price'=>$service->prices[array_key_last($service->prices)]->price
        ]));
    }
}