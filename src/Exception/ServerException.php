<?php

namespace KirsanKifat\ApiServiceBundle\Exception;

class ServerException extends \Exception
{
    protected $message = "Ошибка сервера";

    protected $code = 500;
}