<?php

namespace KirsanKifat\ApiServiceBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roleAdmin = new Role();
        $roleAdmin->setName('admin');
        $manager->persist($roleAdmin);

        $roleUser = new Role();
        $roleUser->setName('user');
        $manager->persist($roleUser);

        $admin = new User();
        $admin->setLogin('test');
        $admin->setEmail('test@gmail.com');
        $admin->setPassword('test');
        $admin->setRole($roleAdmin);
        $admin->setActive(true);
        $manager->persist($admin);

        $user = new User();
        $user->setLogin('test1');
        $user->setEmail('test1@gmail.com');
        $user->setPassword('test1');
        $user->setRole($roleUser);
        $user->setActive(true);
        $manager->persist($user);

        $manager->flush();
    }
}
