<?php namespace View;

use Library\Template;
use Model\Country as Model_Country;

class PageAddService implements \Engine\IView {

    static function render(): string {
        return Base::render(Template::render('/page_add_service.twig',[
            'countries'=>Model_Country::getAllCountries()
        ]));
    }
}