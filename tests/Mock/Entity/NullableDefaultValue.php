<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Entity;

class NullableDefaultValue
{
    public int $id;

    public ?string $default = "default";
}