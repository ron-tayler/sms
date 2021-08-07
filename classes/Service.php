<?php


class Service {
    public int $id = 0;
    public int $country_id = 0;
    public string $name = '';
    public int $count = 0;
    /** @var ServicePrice[] */
    public array $prices = [];
}