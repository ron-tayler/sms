<?php

namespace Library;

require_once __DIR__ . '/db/IAdaptor.php';
require_once __DIR__ . '/db/mpdo.php';
require_once __DIR__ . '/db/mssql.php';
require_once __DIR__ . '/db/mysql.php';
require_once __DIR__ . '/db/mysqli.php';
require_once __DIR__ . '/db/postgre.php';

/**
 * Class DB
 * @package	Library
 * @author Ron_Tayler
 * @copyright 04.05.2021
*/
class DB {
    /** @var DB[] - Список подключений */
    private static array $data = [];
    private DB\IAdaptor $adaptor;

    /**
     * Инициализация подключения или получение из списка
     * @param string $name
     * @param array $param
     * @return DB
     * @throws \ExceptionBase
     */
    public static function init(string $name, array $param = []){
        if(isset(self::$data[$name])){
            return self::$data[$name];
        }elseif($param===[]){
            throw new \ExceptionBase('DB['.$name.'] not init.',5);
        }else{
            if(!isset($param['username'])) throw new \ExceptionBase('Не указан пользователь для подключения к БД',5);
            if(!isset($param['password'])) throw new \ExceptionBase('Не указан пароль для подключения к БД',5);
            if(!isset($param['database'])) throw new \ExceptionBase('Не указано название базы данных',5);
            $adaptor  = $param['adaptor']??'mysqli';
            $hostname = $param['hostname']??'localhost';
            $port = $param['port']??3306;
            $username = $param['username'];
            $password = $param['password'];
            $database = $param['database'];

            try{
                return self::$data[$name] = new self($adaptor, $hostname, $username, $password, $database, $port);
            }catch (\ExceptionBase $ex){
                unset(self::$data[$name]);
                throw new \ExceptionBase($ex->getPrivateMessage(),5,$ex->getMessage(),$ex);
            }
        }
    }

    /**
     * Constructor
     * @param string $adaptor
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int $port
     * @throws \Exception
     */
	private function __construct($adaptor, $hostname, $username, $password, $database, $port = NULL) {
		$class = __NAMESPACE__.'\\DB\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($hostname, $username, $password, $database, $port);
		} else {
			throw new \ExceptionBase('Error: Could not load database adaptor ' . $class . '!',0);
		}
	}

    /**
     * query
     * @param string $sql
     * @return bool|\stdClass
     * @throws \Exception
     */
	public function query($sql) {
		return $this->adaptor->query($sql);
	}

    /**
     * selectAll
     * @param string $tbl
     * @param string $where
     * @param bool $is_return
     * @return bool|\stdClass|string
     * @throws \Exception
     */
	public function selectAll($tbl,$where=null,$is_return=false){
	    return $this->select('*',$tbl,$where,$is_return);
    }

    /**
     * select
     * @param $select
     * @param $tbl
     * @param $where
     * @param bool $is_return
     * @return bool|\stdClass|string
     * @throws \Exception
     */
    public function select($select,$tbl,$where=null,$is_return=false){
        $sql = 'SELECT '.$select.' FROM '.DB_PREFIX.$tbl;
        if(isset($where) and !empty($where)) $sql.=' WHERE '.$where;
        if($is_return)
            return $sql;
        else
            return $this->adaptor->query($sql);
    }

    /**
     * insert_into
     * @param string $tbl
     * @param array $params
     * @return int Last id
     * @throws \Exception
     */
    public function insert_into($tbl,$params){
        $str_keys = implode(', ',array_keys($params));
        $str_params = implode(', ',array_values($params));
	    $sql = 'INSERT INTO '.DB_PREFIX.$tbl.'('.$str_keys.') VALUES ('.$str_params.')';
        $this->query($sql);
        return $this->getLastId();
    }

    /**
     * update
     * @param string $tbl
     * @param array $params
     * @param string $where
     * @throws \Exception
     */
    public function update($tbl,$params,$where){
        $array_params = array();
        foreach($params as $key => $param){
            $array_params[] = '`'.$key.'`='.$param;
        }
        $str_params = implode(', ',$array_params);
        $sql = 'UPDATE '.DB_PREFIX.$tbl.' SET '.$str_params.' WHERE '.$where;
        $this->query($sql);
    }

    /**
     * delete
     * @param $tbl
     * @param $where
     * @param bool $is_return
     * @return bool|\stdClass|string
     * @throws \Exception
     */
    public function delete($tbl,$where,$is_return=false){
        $sql = 'DELETE FROM '.DB_PREFIX.$tbl;
        if(isset($where))$sql.=' WHERE '.$where;
        if($is_return)
            return $sql;
        else
            return $this->adaptor->query($sql);
    }

	/**
     * escape
     * @param	string	$value
	 * @return	string
     */
	public function escape($value) {
		return $this->adaptor->escape($value);
	}

	/**
     * countAffected
	 * @return	int
     */
	public function countAffected() {
		return $this->adaptor->countAffected();
	}

	/**
     * getLastId
	 * @return	int
     */
	public function getLastId() {
		return $this->adaptor->getLastId();
	}

	/**
     * connected
	 * @return	bool
     */	
	public function connected() {
		return $this->adaptor->connected();
	}
}