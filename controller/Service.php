<?php namespace Controller;

use Engine\IController;
use Engine\Request;
use Engine\Response;
use Error\NotFound;
use Model\Service as Model_Service;
use View\PageAddService;
use View\PageEditService;
use View\PageService as View_Page_Service;
use View\PageServices as View_Page_Services;

class Service implements IController {

    static function page(array $param = []){
        $service_id = (int)($param['id']??0);
        if($service_id==0) throw new NotFound();

        $service = Model_Service::getService4Id($service_id);

        Response::setOutput(View_Page_Service::render($service));
    }

    static function list(){
        $services = Model_Service::getAllService();

        Response::setOutput(View_Page_Services::render($services));
    }

    static function add(){
        $method = Request::$server['REQUEST_METHOD'];

        if($method=='GET'){
            Response::setOutput(PageAddService::render());
        }elseif($method=='POST'){
            $country_id = Request::$post['country'];
            $name = Request::$post['name'];
            $price = Request::$post['price'];

            if($country_id==0 or empty($name) or empty($price)) throw new \Error\Request();

            Model_Service::addService($country_id,$name,$price);
            Response::redirect('/services');
        }

    }

    static function edit(array $param = []){
        $service_id = (int)($param['id']??0);
        $method = Request::$server['REQUEST_METHOD'];

        if($method=='GET'){
            $service = Model_Service::getService4Id($service_id);
            Response::setOutput(PageEditService::render($service));
        }elseif($method=='POST'){
            $price = Request::$post['price'];

            if(empty($price)) throw new \Error\Request();

            Model_Service::editPrice4id($service_id,$price);
            Response::redirect('/services');
        }
    }

    static function delete(array $param = []){
        $service_id = (int)($param['id']??0);

        Model_Service::deleteService4Id($service_id);
        Response::redirect('/services');
    }
}