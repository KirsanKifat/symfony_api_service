<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\Collection;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\EditCollectionRequest;

class EditByObject
{
    public static function get(): EditCollectionRequest
    {
        $obj = new EditCollectionRequest();
        $obj->name = 'test';
        $obj->collection = [1,2];

        return $obj;
    }
}