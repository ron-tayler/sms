<?php namespace Model;

use Engine\IModel;
use Error\NotFound;
use Library\DB;

class Service implements IModel {

    /**
     * @return \Service[]
     * @throws \ExceptionBase
     */
    static function getAllService(){
        $sql = "SELECT * FROM services";
        $db_services = DB::init('base')->query($sql)->rows;

        return self::initServices4dbResult($db_services);
    }

    /**
     * @param int $country_id
     * @return \Service[]
     * @throws \ExceptionBase
     */
    static function getServices4CountryId(int $country_id){
        $sql = "SELECT * FROM services WHERE country_id={$country_id}";
        $db_services = DB::init('base')->query($sql)->rows;

        return self::initServices4dbResult($db_services);

    }

    static function getService4Id(int $service_id){
        $sql = "SELECT * FROM services WHERE id={$service_id}";
        $db_result = DB::init('base')->query($sql);

        if($db_result->num_rows==0) throw new NotFound();

        return self::initService4dbResult($db_result->row);
    }

    static function addService(int $country_id, string $name, int $price){
        $sql = "INSERT INTO services(country_id,name) VALUES ({$country_id},'{$name}')";
        DB::init('base')->query($sql);

        $service_id = DB::init('base')->getLastId();
        $sql = "INSERT INTO service_price(service_id, price) VALUES ({$service_id},{$price})";
        DB::init('base')->query($sql);
    }

    static function editPrice4id(int $service_id, int $new_price){
        $sql = "INSERT INTO service_price(service_id, price) VALUES ({$service_id},{$new_price})";
        DB::init('base')->query($sql);
    }

    static function deleteService4Id(int $service_id){
        $sql = "DELETE FROM services WHERE id={$service_id}";
        DB::init('base')->query($sql);
    }

    private static function initService4dbResult($db_service){
        $service = new \Service();
        $service->id = $db_service['id'];
        $service->country_id = $db_service['country_id'];
        $service->name = $db_service['name'];
        $service->count = $db_service['count'];

        $sql = "SELECT * FROM service_price WHERE service_id={$service->id}";
        $db_result = DB::init('base')->query($sql);

        if($db_result->num_rows>0){
            $db_prices = $db_result->rows;
            foreach ($db_prices as $db_price){
                $price = new \ServicePrice();
                $price->id = $db_price['id'];
                $price->price = $db_price['price'];
                $service->prices[$price->id] = $price;
            }
        }else{
            $service->prices[0] = new \ServicePrice();
        }

        return $service;
    }
    private static function initServices4dbResult($db_services){
        $services = [];
        foreach ($db_services as $db_service){
            $service = self::initService4dbResult($db_service);
            $services[$service->id] = $service;
        }
        return $services;
    }

}