<?php namespace View;


use Library\Template;

class Login implements \Engine\IView {

    static function render(): string{
        Template::setTitle('Авторизация');
        return Base::render(Template::render('/login.twig',[]));
    }
}