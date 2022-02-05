<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserClass;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserEntityArray;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;
use KirsanKifat\ApiServiceBundle\Serializer\EntityObjectSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityObjectSerializerTest extends KernelTestCase
{
    private EntityObjectSerializer $serializer;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        /** @var EntityManagerInterface $em */
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();


        $this->serializer = new EntityObjectSerializer();
    }

    public function testToArray()
    {
        $array = $this->serializer->toArray(UserClass::get());

        $this->assertEquals($array, UserArray::get());
    }

    public function testEntityToArray()
    {
        $entity = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $this->assertEquals($this->serializer->toArray($entity), UserEntityArray::get());
    }

    public function testToObject()
    {
        $object = $this->serializer->toObject(UserArray::get(), User::class);



        $this->assertTrue(true);
    }
}