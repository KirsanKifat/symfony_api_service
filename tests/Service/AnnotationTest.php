<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Serializer\ReflectionHelper;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\Logger;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\EditArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\EditObjectRequest;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\UserCreateArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\UserCreateRequest;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\EntityForCollection;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\EntityWithCollection;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RoleWIthOnlyDoctrineAnnotation;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithAnnotation;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithOnlyDoctrineAnnotation;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Service\CollectionService;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Service\UserWithAnnotationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnnotationTest extends KernelTestCase
{
    private UserWithAnnotationService $serviceAnnotation;

    private CollectionService $collectionService;

    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->collectionService = new CollectionService($this->em, new Logger());
        $this->serviceAnnotation = new UserWithAnnotationService($this->em, new Logger());
    }

    public function testCreateWithAnnotationFromArray()
    {
        $user = $this->serviceAnnotation->create(UserCreateArray::get());

        $userByDB = $this->em->getRepository(UserWithAnnotation::class)->findOneBy(['login' => 'my_user']);

        $this->assertEquals($user, $userByDB);
    }


    public function testCreateWithAnnotationFromObject()
    {
        $user = $this->serviceAnnotation->create(UserCreateRequest::get());

        $userByDB = $this->em->getRepository(UserWithAnnotation::class)->findOneBy(['login' => 'my_user']);

        $this->assertEquals($user, $userByDB);
    }

    public function testEditWithAnnotationFromArray()
    {
        $user = $this->serviceAnnotation->edit(EditArray::get());

        $userByDB = $this->em->getRepository(UserWithAnnotation::class)->findOneBy(['login' => 'new']);

        $this->assertEquals($userByDB, $user);
        $this->assertEquals('new', $userByDB->getLogin());
        $this->assertEquals(2, $userByDB->getRole()->getId());
    }

    public function testEditWithAnnotationFromObject()
    {
        $user = $this->serviceAnnotation->edit(EditObjectRequest::get());

        $userByDB = $this->em->getRepository(UserWithAnnotation::class)->findOneBy(['login' => 'new']);

        $this->assertEquals($userByDB, $user);
        $this->assertEquals('new', $userByDB->getLogin());
        $this->assertEquals(2, $userByDB->getRole()->getId());
    }

    public function testGetAnnotationDoctrine()
    {
        $propertyType = ReflectionHelper::getPropertyType(new UserWithOnlyDoctrineAnnotation(), 'role');
        $this->assertEquals(RoleWIthOnlyDoctrineAnnotation::class, $propertyType);

        $propertyType = ReflectionHelper::getPropertyType(new UserWithOnlyDoctrineAnnotation(), 'password');
        $this->assertEquals('string', $propertyType);
    }

    public function testGetAnnotationDoctrineCollection()
    {
        $propertyType = ReflectionHelper::getPropertyType(new EntityWithCollection(), 'collection');

        $this->assertEquals(ArrayCollection::class, $propertyType);

        $collectionType = ReflectionHelper::getArrayCollectionPropertyTarget(new EntityWithCollection(), 'collection');
        $this->assertEquals(EntityForCollection::class, $collectionType);
    }
}