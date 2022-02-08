<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\RoleResponse;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\UserResponse;

class UserTestResponse
{
    public static function get(): UserResponse
    {
        $user = new UserResponse();
        $user->id = 1;
        $user->login = 'test';
        $user->email = 'test@gmail.com';
        $role = new RoleResponse();
        $role->name = 'admin';
        $user->role = $role;

        return $user;
    }
}