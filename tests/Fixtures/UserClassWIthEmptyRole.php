<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;

class UserClassWIthEmptyRole
{
    public static function get(): User
    {
        $user = new User();
        $user->setLogin("test");
        $user->setPassword("test");
        $user->setEmail("test@gmail.com");
        $user->setActive(true);

        return $user;
    }
}