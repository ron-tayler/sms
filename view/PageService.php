<?php namespace View;

use Engine\IView;
use Library\Template;
use Model\Country;

class PageService implements IView {

    static function render(\Service $service = null): string{
        $view_service = [
            'name'=>$service->name,
            'country'=>Country::getCountry4id($service->country_id)->name,
            'count'=>$service->count,
            'prices'=>array_reverse($service->prices)
        ];
        return Base::render(Template::render('/page_service.twig',[
            'service'=>$view_service
        ]));
    }
}