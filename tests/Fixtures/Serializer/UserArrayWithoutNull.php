<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

class UserArrayWithoutNull
{
    public static function get(): array
    {
        return [
            'login' => 'test',
            'password' => 'test',
            'email' => 'test@gmail.com',
            'role' => ['name' => 'admin'],
            'active' => true
        ];
    }
}