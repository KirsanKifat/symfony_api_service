<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\DefaultValueIsNull;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\ExtendedNullableDefaultValueObject;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Logger;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\RecursionEntityObject;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UpdateUserArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UpdateUserObject;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserAfterUpdate;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserArrayWithoutNull;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserClass;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserClassWIthEmptyRole;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserEntityArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\UserResponseObject;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\UserResponse;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\ExtendedNullableDefaultValue;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\NullableDefaultValue;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursionEntity;
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



        $this->serializer = new EntityObjectSerializer(new Logger());
    }

    public function testToArray()
    {
        $array = $this->serializer->toArray(UserClass::get());

        $this->assertEquals(UserArray::get(), $array);
    }

    public function testFailToArrayWithRecursionObject()
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

        $this->assertEquals($resultArr, $array);
    }

    public function testToArrayWithRemoveNullValue()
    {
        $array = $this->serializer->toArray(UserClass::get(), false);

        $this->assertEquals(UserArrayWithoutNull::get(), $array);
    }

    public function testEntityToArray()
    {
        $entity = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $this->assertEquals(UserEntityArray::get(), $this->serializer->toArray($entity));
    }

    public function testArrayToObject()
    {
        $object = $this->serializer->toObject(UserArray::get(), User::class);

        $this->assertEquals(UserClass::get(),$object);
    }

    public function testSetNullInNullableDefaultValue()
    {
        $object = $this->serializer->toObject(['id' => 1, 'default' => null], NullableDefaultValue::class);

        $this->assertEquals( DefaultValueIsNull::get(), $object);
    }

    public function testNotSetNullInDefaultValue()
    {
        $object = $this->serializer->toObject(['id' => 1, 'default' => null], NullableDefaultValue::class, false);

        $expect = DefaultValueIsNull::get();
        $expect->default = 'default';
        $this->assertEquals($expect , $object);
    }

    public function testObjectToObject()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $response = $this->serializer->toObject($user, UserResponse::class);

        $this->assertEquals(UserResponseObject::get(), $response);
    }

    public function testSetNullInNullableDefaultValueFromObject()
    {
        $object = $this->serializer->toObject(ExtendedNullableDefaultValueObject::get(), NullableDefaultValue::class);

        $this->assertEquals( DefaultValueIsNull::get(), $object);
    }

    /**
     * @return void
     * @throws ServerException
     * @group my
     */
    public function testNotSetNullInDefaultValueFromObject()
    {
        $object = $this->serializer->toObject(ExtendedNullableDefaultValueObject::get(), NullableDefaultValue::class, false);

        $expect = DefaultValueIsNull::get();
        $expect->default = 'default';
        $this->assertEquals($expect , $object);
    }

    public function testUpdateObjectFromArray()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        /** @var User $result */
        $result = $this->serializer->updateObject(UpdateUserArray::get(), $user);

        $role = $this->em->getRepository(Role::class)->findOneBy(['name' => 'admin']);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals('new', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals($role, $result->getRole());
        $this->assertEquals('my_email@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

//    public function testUpdateObjectFromObject()
//    {
//        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);
//
//        $result = $this->serializer->updateObject(UpdateUserObject::get(), $user);
//
//        /** @var User $result */
//        $role = $this->em->getRepository(Role::class)->findOneBy(['name' => 'admin']);
//
//        $this->assertEquals(1, $result->getId());
//        $this->assertEquals('new', $result->getLogin());
//        $this->assertEquals('test', $result->getPassword());
//        $this->assertEquals($role, $result->getRole());
//        $this->assertEquals('my_email@gmail.com', $result->getEmail());
//        $this->assertEquals(true, $result->getActive());
//    }
}