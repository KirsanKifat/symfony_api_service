<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursionEntity;

class RecursionEntityObject
{
    public static function get(): RecursionEntity
    {
        $entity = new RecursionEntity();

        $subEntity = new RecursionEntity();

        $entity->id = 1;

        $subEntity->id = 2;

        $entity->entity = $subEntity;

        return $entity;
    }
}