<?php

namespace Engine;
/**
 * Interface IController
 * @package YouInRoll.com
 * @author Ron_Tayler
 * @copyright 2021
 */
interface IController {
    static function init();
    static function index(array $param = []);
}