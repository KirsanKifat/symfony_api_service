<?php

namespace KirsanKifat\ApiServiceBundle\Exception;

class IncorrectParamsException extends \Exception
{
    protected $message = "Не корректные параметры запроса";

    protected $code = 400;
}