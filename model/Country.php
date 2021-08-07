<?php namespace Model;

use Engine\IModel;
use Error\NotFound;
use Library\DB;

class Country implements IModel{

    /**
     * @return \Country[]
     * @throws \ExceptionBase
     */
    static function getAllCountries(): array{
        $sql = "SELECT * FROM countries";
        $db_result = DB::init('base')->query($sql);
        $countries = [];
        foreach ($db_result->rows as $db_country){
            $country = new \Country;
            $country->id = $db_country['id'];
            $country->name = $db_country['name'];
            $countries[$country->id] = $country;
        }

        return $countries;
    }

    static function getCountry4id(int $id): \Country{
        $sql = "SELECT * FROM countries WHERE id={$id}";
        $db_result = DB::init('base')->query($sql);
        if($db_result->num_rows==0) throw new NotFound();
        $country = new \Country();
        $country->id = $db_result->row['id'];
        $country->name = $db_result->row['name'];
        return $country;
    }

    static function addCountry(string $name): int{
        $sql = "INSERT INTO countries(name) VALUES ('{$name}')";

        DB::init('base')->query($sql);
        return DB::init('base')->getLastId();
    }

    static function deleteCountry4id(int $id){
        $sql = "SELECT * FROM countries WHERE id={$id}";
        if(DB::init('base')->query($sql)->num_rows==0) throw new NotFound();

        $sql = "DELETE FROM countries WHERE id={$id}";
        DB::init('base')->query($sql);

        if(DB::init('base')->query($sql)->num_rows!=0) throw new \Error\Unknown();
    }
}