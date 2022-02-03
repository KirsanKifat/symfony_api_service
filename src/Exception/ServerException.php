<?php

namespace KirsanKifat\ApiServiceBundle\Exception;

class ServerException extends \Exception
{
    protected $message = "Ошибка сервер";

    protected $code = 500;
}