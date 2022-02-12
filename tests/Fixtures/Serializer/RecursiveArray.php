<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

class RecursiveArray
{
    public static function get(): array
    {
        return [
            'id' => 1,
            'subObject' => [
                'id' => 1,
                'subObject' => null
            ]
        ];
    }
}