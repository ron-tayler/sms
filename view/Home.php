<?php namespace View;

use Engine\IView;
use Library\Template;
use Model\Country as Model_Country;

class Home implements IView {

    static function render(array $cards = [],int $country_id = 0): string{

        Template::setTitle('Главная');

        $render_home = Template::render('/home.twig',[
            'select_country_id'=>$country_id,
            'countries'=>Model_Country::getAllCountries(),
            'cards'=>$cards
        ]);

        return Base::render($render_home);
    }
}