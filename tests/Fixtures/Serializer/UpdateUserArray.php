<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

class UpdateUserArray
{
    public static function get(): array
    {
        return [
            'login' => 'new',
            'email' => 'my_email@gmail.com'
        ];
    }
}