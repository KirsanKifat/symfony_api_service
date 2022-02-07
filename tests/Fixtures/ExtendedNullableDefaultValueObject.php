<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\ExtendedNullableDefaultValue;

class ExtendedNullableDefaultValueObject
{
    public static function get(): ExtendedNullableDefaultValue
    {
        $object = new ExtendedNullableDefaultValue();

        $object->id = 1;

        $object->default = null;

        $object->property = "asdfgggh";

        return $object;
    }
}