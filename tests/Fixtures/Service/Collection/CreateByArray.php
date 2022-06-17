<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\Collection;

class CreateByArray
{
    public static function get(): array
    {
        return [
            'name' => 'test',
            'collection' => [1,3]
        ];
    }
}