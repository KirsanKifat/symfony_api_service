<?php

namespace KirsanKifat\ApiServiceBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RoleWithAnnotation;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithAnnotation;

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

        $roleWithAnnotationAdmin = new RoleWithAnnotation();
        $roleWithAnnotationAdmin->setName('admin');
        $manager->persist($roleWithAnnotationAdmin);

        $roleWithAnnotationUser = new RoleWithAnnotation();
        $roleWithAnnotationUser->setName('user');
        $manager->persist($roleWithAnnotationUser);

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

        $admin = new UserWithAnnotation();
        $admin->setLogin('test');
        $admin->setEmail('test@gmail.com');
        $admin->setPassword('test');
        $admin->setRole($roleWithAnnotationAdmin);
        $admin->setActive(true);
        $manager->persist($admin);

        $user = new UserWithAnnotation();
        $user->setLogin('test1');
        $user->setEmail('test1@gmail.com');
        $user->setPassword('test1');
        $user->setRole($roleWithAnnotationUser);
        $user->setActive(true);
        $manager->persist($user);

        $manager->flush();
    }
}
