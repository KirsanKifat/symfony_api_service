<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\NullableDefaultValue;

class DefaultValueIsNull
{
    public static function get(): NullableDefaultValue
    {
        $object = new NullableDefaultValue();

        $object->id = 1;
        $object->default = null;

        return $object;
    }
}