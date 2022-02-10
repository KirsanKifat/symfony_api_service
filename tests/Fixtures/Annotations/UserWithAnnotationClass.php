<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Annotations;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RoleWithAnnotation;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithAnnotation;

class UserWithAnnotationClass
{
    public static function get(): UserWithAnnotation
    {
        $user = new UserWithAnnotation();
        $user->setLogin("test");
        $user->setPassword("test");
        $user->setEmail("test@gmail.com");
        $role = new RoleWithAnnotation();
        $role->setName("admin");
        $user->setRole($role);
        $user->setActive(true);

        return $user;
    }
}