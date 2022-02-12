<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Dto;

use JMS\Serializer\Annotation as Serializer;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursiveObjectTwo;

class RecursiveObjectRequest
{
    public $id;

    /**
     * @Serializer\Type("KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursiveObjectTwo")
     */
    public $subObject;
}