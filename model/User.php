<?php namespace Model;

use Engine\IModel;
use Error\NotFound;
use Library\DB;

class User implements IModel {

    static DB $db;

    static function init(){
        self::$db = DB::init('base');
    }

    static function getUser4id(int $user_id){
        $sql = "SELECT * FROM users WHERE id={$user_id}";
        $user_db = DB::init('base')->query($sql);

        if($user_db->num_rows==0) throw new NotFound('Не найден пользователь в БД',true);

        return [
            'id' => $user_db->row['id'],
            'group_id' => $user_db->row['group_id'],
            'name' => $user_db->row['name']
        ];
    }
}