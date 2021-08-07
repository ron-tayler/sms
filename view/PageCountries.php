<?php namespace View;

use Engine\IView;
use Library\Template;
use Model\Country;

class PageCountries implements IView {

    static function render($countries = []): string{
        $view_countries = [];
        foreach ($countries as $country){
            $view_countries []= [
                'id'=>$country->id,
                'name'=>$country->name,
            ];
        }
        return Base::render(Template::render('page_countries.twig',[
            'countries'=>$view_countries
        ]));
    }
}