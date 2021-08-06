<?php

namespace Engine;
/**
* Request class
 * @package Engine
 * @author Ron_Tayler
 * @copyright 04.05.2021
 */
class Request {
	public static array $get = [];
	public static array $post = [];
	public static array $request = [];
	public static array $cookie = [];
	public static array $files = [];
	public static array $server = [];
	
	/**
	 * Request init
 	 */
	public static function init() {
		self::$get = self::clean($_GET);
        $post_json = file_get_contents('php://input');
        $post_obj = json_decode($post_json);
        if(json_last_error() == JSON_ERROR_NONE){
            $post_array = [];
            foreach ($post_obj as $post_arg_key=>$post_arg_value) $post_array[$post_arg_key] = $post_arg_value;
            self::$post = $post_array;
        }else{
            self::$post = $_POST;
        }
        self::$request = self::clean($_REQUEST);
        self::$cookie = self::clean($_COOKIE);
        self::$files = self::clean($_FILES);
        self::$server = self::clean($_SERVER);
	}
	
	/**
     * Method clean - очистка спец. символов html
	 * @param array|string $data
     * @return array|string
     */
	private static function clean($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);

				$data[self::clean($key)] = self::clean($value);
			}
		} else {
			$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
		}

		return $data;
	}
}