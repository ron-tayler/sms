<?php namespace View;

use Engine\IView;
use Library\Template;

class Base implements IView {

    static function render(string $render_page = ''): string{
        return Template::render('/base.twig',[
            'base_url'=>SITE_URL,
            'title'=>Template::getTitle(),
            'header'=>Header::render(),
            'page'=>$render_page
        ]);
    }
}