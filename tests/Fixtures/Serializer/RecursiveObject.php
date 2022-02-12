<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursiveObjectOne;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursiveObjectTwo;

class RecursiveObject
{
    public static function get(): RecursiveObjectOne
    {
        $one = new RecursiveObjectOne();
        $two = new RecursiveObjectTwo();

        $reflectionOne = new \ReflectionClass($one);
        $reflectionTwo = new \ReflectionClass($two);

        $propertyOne = $reflectionOne->getProperty('id');
        $propertyOne->setAccessible(true);
        $propertyOne->setValue($one, 1);

        $propertyTwo = $reflectionTwo->getProperty('id');
        $propertyTwo->setAccessible(true);
        $propertyTwo->setValue($two, 1);

        $one->setSubObject($two);

        return $one;
    }
}