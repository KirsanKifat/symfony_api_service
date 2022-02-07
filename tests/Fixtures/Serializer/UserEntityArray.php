<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

class UserEntityArray
{
    public static function get(): array
    {
        return [
            'id' => 1,
            'login' => 'test',
            'password' => 'test',
            'email' => 'test@gmail.com',
            'role' => ['id' => 1, 'name' => 'admin'],
            'active' => true
        ];
    }
}