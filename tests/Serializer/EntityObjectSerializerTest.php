<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\DefaultValueIsNull;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Logger;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\RecursionEntityObject;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserClass;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserClassWIthEmptyRole;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserEntityArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserResponseObject;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\UserResponse;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\NullableDefaultValue;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursionEntity;
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



        $this->serializer = new EntityObjectSerializer(new Logger());
    }

    public function testToArray()
    {
        $array = $this->serializer->toArray(UserClass::get());

        $this->assertEquals($array, UserArray::get());
    }

    public function testToArrayRecursionObject()
    {
        try {
            $this->serializer->toArray(RecursionEntityObject::get());
            $this->assertTrue(false, 'Должно быть выброшено исключение');
        } catch (ServerException $e) {
            $this->assertTrue(true);
        }
    }

    public function testToArrayWithEmptySubObject()
    {
        $array = $this->serializer->toArray(UserClassWIthEmptyRole::get());

        $resultArr = UserArray::get();
        $resultArr['role'] = null;

        $this->assertEquals($array, $resultArr);
    }

    public function testEntityToArray()
    {
        $entity = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $this->assertEquals($this->serializer->toArray($entity), UserEntityArray::get());
    }


    public function testArrayToObject()
    {
        $object = $this->serializer->toObject(UserArray::get(), User::class);

        $this->assertEquals($object, UserClass::get());
    }

    public function testSetNullInNullableDefaultValue()
    {
        $object = $this->serializer->toObject(['id' => 1, 'default' => null], NullableDefaultValue::class);

        $this->assertEquals($object, DefaultValueIsNull::get());
    }

    public function testObjectToObject()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $response = $this->serializer->toObject($user, UserResponse::class);

        $this->assertEquals($response, UserResponseObject::get());
    }


}