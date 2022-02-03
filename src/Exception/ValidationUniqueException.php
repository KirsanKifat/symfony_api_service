<?php

namespace KirsanKifat\ApiServiceBundle\Exception;

use Throwable;

class ValidationUniqueException extends \Exception
{
    public function __construct($parameter, Throwable $previous = null)
    {
        $message = 'Параметр ' . $parameter . ' не соответствует критериям уникальности.';
        parent::__construct($message, 400, $previous);
    }
}