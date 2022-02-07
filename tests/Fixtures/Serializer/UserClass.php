<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;

class UserClass
{
   public static function get(): User
   {
       $user = new User();
       $user->setLogin("test");
       $user->setPassword("test");
       $user->setEmail("test@gmail.com");
       $role = new Role();
       $role->setName("admin");
       $user->setRole($role);
       $user->setActive(true);

       return $user;
   }
}
