<?php namespace View;

use Engine\IView;
use Library\Template;

class PageAddCountry implements IView {

    static function render(): string{
        return Base::render(Template::render('/page_add_country.twig', []));
    }
}