<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Entity;

class RecursionEntity
{
    public int $id;

    public RecursionEntity $entity;
}