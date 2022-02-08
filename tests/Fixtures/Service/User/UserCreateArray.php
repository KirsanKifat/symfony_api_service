<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User;

class UserCreateArray
{
    public static function get(): array
    {
        return [
            'login' => 'my_user',
            'password' => 'my_user',
            'email' => 'my_user@gmail.com',
            'role' => 2,
            'active' => true
        ];
    }
}