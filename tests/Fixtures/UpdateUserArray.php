<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures;

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