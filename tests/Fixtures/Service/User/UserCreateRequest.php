<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\CreateRequest;

class UserCreateRequest
{
    public static function get(): CreateRequest
    {
        $user = new CreateRequest();

        $user->login = 'my_user';
        $user->password = 'my_user';
        $user->email = "my_user@gmail.com";
        $user->role = 2;
        $user->active = true;

        return $user;
    }
}