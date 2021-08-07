<?php


namespace View;


use Engine\IView;
use Library\Template;
use Library\User;

class Header implements IView {

    static function render(): string{
        return Template::render('/header.twig',[
            'logo_name'=>'SMS',
            'user'=>User::is_user() && [
                'name'=>User::getName()
            ],
            'is_perm_countries'=>true,
            'is_perm_services'=>true
        ]);
    }
}