<?php

namespace KirsanKifat\ApiServiceBundle\Exception;

class ObjectNotFoundException extends \Exception
{
    protected $message = "Объект не найден";

    protected $code = 400;
}