<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\Collection;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\CreateCollectionRequest;

class CreateByObject
{
    public static function get()
    {
        $obj = new CreateCollectionRequest();
        $obj->name = 'test';
        $obj->collection = [1,3];

        return $obj;
    }
}