<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;

class UserTestEntity
{
    public static function get()
    {
        $user = new User();

        $reflectionClass = new \ReflectionClass($user);
        $id = $reflectionClass->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($user, 1);
        $user->setLogin('test');
        $user->setPassword('test');
        $user->setEmail('test@gmail.com');
        $role = new Role();
        $reflectionClass = new \ReflectionClass($role);
        $id = $reflectionClass->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($role, 1);
        $role->setName('admin');
        $user->setRole($role);
        $user->setActive(true);

        return $user;
    }
}