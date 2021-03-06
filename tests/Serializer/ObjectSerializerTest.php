<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Serializer\ObjectSerializer;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Annotations\UserWithAnnotationClass;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\DefaultValueIsNull;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\ExtendedNullableDefaultValueObject;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\Logger;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\RecursiveArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\RecursiveObject;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\UpdateUserArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\UpdateUserObject;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\UserArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\UserArrayWithoutNull;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\UserClass;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\UserClassWIthEmptyRole;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\UserEntityArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\UserResponseObject;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\RecursiveObjectRequest;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\UserResponse;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\NullableDefaultValue;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursiveObjectOne;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RoleWithAnnotation;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithAnnotation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ObjectSerializerTest extends KernelTestCase
{
    private ObjectSerializer $serializer;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        /** @var EntityManagerInterface $em */
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();



        $this->serializer = new ObjectSerializer(new Logger());
    }

    public function testToArray()
    {
        $array = $this->serializer->toArray(UserClass::get());

        $this->assertEquals(UserArray::get(), $array);
    }

    public function testToArrayWithRoleTypeOnlyAnnotation()
    {
        $array = $this->serializer->toArray(UserWithAnnotationClass::get());

        $this->assertEquals(UserArray::get(), $array);
    }

    public function testEntityToArrayWithRoleTypeOnlyAnnotation()
    {
        $entity = $this->em->getRepository(UserWithAnnotation::class)->findOneBy(['login' => 'test']);

        $array = $this->serializer->toArray($entity);

        $this->assertEquals(UserEntityArray::get(), $array);
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

    public function testEntityToArrayRecursive()
    {
        $entity = $this->em->getRepository(RecursiveObjectOne::class)->find(1);

        set_time_limit(3);
        $result = $this->serializer->toArray($entity);

        $this->assertEquals(RecursiveArray::get(), $result);
    }

    public function testArrayToObject()
    {
        $object = $this->serializer->toObject(UserArray::get(), User::class);

        $this->assertEquals(UserClass::get(),$object);
    }

    public function testArrayToEntityWithAnnotation()
    {
        $object = $this->serializer->toObject(UserArray::get(), UserWithAnnotation::class);

        $this->assertEquals(UserWithAnnotationClass::get(),$object);
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

    public function testArrayToEntityRecursive()
    {
        set_time_limit(3);
        $object = $this->serializer->toObject(RecursiveArray::get(), RecursiveObjectOne::class, false);

        $this->assertEquals(RecursiveObject::get(), $object);
    }

    public function testObjectToObject()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $response = $this->serializer->toObject($user, UserResponse::class);

        $this->assertEquals(UserResponseObject::get(), $response);
    }

    public function testObjectToObjectWithAnnotations()
    {
        $response = $this->serializer->toObject(UserClass::get(), UserWithAnnotation::class);

        $this->assertEquals(UserWithAnnotationClass::get(), $response);
    }

    public function testObjectWithAnnotationsToObject()
    {
        $user = $this->em->getRepository(UserWithAnnotation::class)->findOneBy(['login' => 'test']);

        $response = $this->serializer->toObject($user, UserResponse::class);

        $this->assertEquals(UserResponseObject::get(), $response);
    }

    public function testSetNullInNullableDefaultValueFromObject()
    {
        $object = $this->serializer->toObject(ExtendedNullableDefaultValueObject::get(), NullableDefaultValue::class);

        $this->assertEquals( DefaultValueIsNull::get(), $object);
    }

    public function testNotSetNullInDefaultValueFromObject()
    {
        $object = $this->serializer->toObject(ExtendedNullableDefaultValueObject::get(), NullableDefaultValue::class, false);

        $expect = DefaultValueIsNull::get();
        $expect->default = 'default';
        $this->assertEquals($expect , $object);
    }

    public function testEntityToEntityRecursive()
    {
        $one = $this->em->getRepository(RecursiveObjectOne::class)->find(1);

        set_time_limit(3);
        $result = $this->serializer->toObject($one, RecursiveObjectRequest::class);
        $this->assertTrue(true);
    }

    public function testUpdateObjectFromArray()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        /** @var User $result */
        $result = $this->serializer->updateObject(UpdateUserArray::get(), $user);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals('new', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals(1, $result->getRole()->getId());
        $this->assertEquals('user', $result->getRole()->getName());
        $this->assertEquals('my_email@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

    public function testUpdateObjectWithAnnotationFromArray()
    {
        $user = $this->em->getRepository(UserWithAnnotation::class)->findOneBy(['login' => 'test']);

        /** @var User $result */
        $result = $this->serializer->updateObject(UpdateUserArray::get(), $user);


        $this->assertEquals(1, $result->getId());
        $this->assertEquals('new', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals(1, $result->getRole()->getId());
        $this->assertEquals('user', $result->getRole()->getName());
        $this->assertEquals('my_email@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

    public function testUpdateObjectFromObject()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        /** @var User $result */
        $result = $this->serializer->updateObject(UpdateUserObject::get(), $user);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals('new', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals(1, $result->getRole()->getId());
        $this->assertEquals('user', $result->getRole()->getName());
        $this->assertEquals('my_email@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

    public function testUpdateObjectWithAnnotationFromObject()
    {
        $user = $this->em->getRepository(UserWithAnnotation::class)->findOneBy(['login' => 'test']);

        $result = $this->serializer->updateObject(UpdateUserObject::get(), $user);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals('new', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals(1, $result->getRole()->getId());
        $this->assertEquals('user', $result->getRole()->getName());
        $this->assertEquals('my_email@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

    public function testSetNullForUpdateObjectFromArr()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        /** @var User $result */
        $result = $this->serializer->updateObject(['login' => null], $user);

        $role = $this->em->getRepository(Role::class)->findOneBy(['name' => 'admin']);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals(null, $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals($role, $result->getRole());
        $this->assertEquals('test@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

    public function testSetNullForUpdateObjectFromObject()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $updateObj = UpdateUserObject::get();
        $updateObj->email = null;

        /** @var User $result */
        $result = $this->serializer->updateObject($updateObj, $user);

        $role = $this->em->getRepository(Role::class)->findOneBy(['name' => 'admin']);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals('new', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals($role, $result->getRole());
        $this->assertEquals(null, $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

    public function testDontSetNulForUpdateObjFromArr()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        /** @var User $result */
        $result = $this->serializer->updateObject(['login' => null], $user, false);

        $role = $this->em->getRepository(Role::class)->findOneBy(['name' => 'admin']);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals('test', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals($role, $result->getRole());
        $this->assertEquals('test@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

    public function testDontSetNullForUpdateObjFromObj()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $updateObj = UpdateUserObject::get();
        $updateObj->email = null;

        /** @var User $result */
        $result = $this->serializer->updateObject($updateObj, $user, false);

        $role = $this->em->getRepository(Role::class)->findOneBy(['name' => 'admin']);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals('new', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals($role, $result->getRole());
        $this->assertEquals('test@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }

    public function testSetNullOnNotNullableProperty()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $updateObj = UpdateUserObject::get();
        $updateObj->role = null;

        /** @var User $result */
        $result = $this->serializer->updateObject($updateObj, $user, false);

        $role = $this->em->getRepository(Role::class)->findOneBy(['name' => 'admin']);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals('new', $result->getLogin());
        $this->assertEquals('test', $result->getPassword());
        $this->assertEquals($role, $result->getRole());
        $this->assertEquals('my_email@gmail.com', $result->getEmail());
        $this->assertEquals(true, $result->getActive());
    }
}