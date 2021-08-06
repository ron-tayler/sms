<?php
/**
 * Class ExceptionConfig
 * Класс исключений для подключения конфигураций
 * @package YouInRoll.com
 * @author Ron_Tayler
 * @copyright 2021
 */
class ExceptionConfig extends ExceptionEngine{
    private string $paramName;

    /**
     * ExceptionConfig constructor.
     * @param string $paramName Приватное название параметра
     * @param string $message Приватное сообщение об ошибке для логов
     * @param int $code Публичный код ошибки
     * @param Throwable|null $previous
     */
    public function __construct(string $paramName = '',string $message = '', int $code = 0,Throwable $previous = null){
        $this->paramName = $paramName;
        $code = ($code<10)? $code+10 :1; // Ошибки конфигураций 10+
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getParam(){
        return $this->paramName;
    }
}