<?php
namespace Error;

use Error;
use Throwable;

abstract class Base extends Error{
    protected const CODE = 0;
    protected const MESSAGE = 'Неизвестная ошибка';
    private string $private_message = '';

    /**
     * Error\Base constructor.
     * @param string $private_message Приватное сообщение
     * @param string|bool $public_message Публичное сообщение
     * @param Throwable|null $previous Ветка исключений
     */
    public final function __construct(string $private_message = null, $public_message = null, Throwable $previous = null){
        $code = static::CODE;
        $this->private_message = $private_message ?? static::MESSAGE;
        if(is_bool($public_message) && $public_message){
            $public_message = $this->private_message;
        }else{
            $public_message ??= static::MESSAGE;
        }

        parent::__construct($public_message, $code, $previous);
    }

    public final function getPrivateMessage(){
        return $this->private_message;
    }
}