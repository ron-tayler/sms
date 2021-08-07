<?php namespace Library;


class User{
    private static int $id = 0;
    private static string $name = '';

    static function tokenAuth($token){
        $db = DB::init('base');
        $res = $db->select('user_id','users_tokens','token=\''.$token.'\'');
        if($res->num_rows>0){
            self::$id = $res->row['user_id'];
            self::$name = $res->row['user_id'];
        }else{
            self::$id = 0;
            self::$name = 0;
        }
    }

    static function is_user(){
        return (bool)self::$id;
    }

    static function getId(){
        return self::$id;
    }

    static function getName(){
        return self::$name;
    }

}
