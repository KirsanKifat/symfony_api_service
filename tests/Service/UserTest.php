<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Exception\IncorrectParamsException;
use KirsanKifat\ApiServiceBundle\Exception\ObjectNotFoundException;
use KirsanKifat\ApiServiceBundle\Exception\ValidationUniqueException;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\Logger;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\EditArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\EditObjectRequest;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\UserCreateArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\UserCreateRequest;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\UserTestEntity;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User\UserTestResponse;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\UserResponse;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private UserService $service;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        /** @var EntityManagerInterface $em */
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->service = new UserService($this->em, new Logger());
    }

    public function testGetUser()
    {
        $user = $this->service->get(['login' => 'test']);

        $this->assertEquals(UserTestEntity::get(), $user);
    }

    public function testGetByRole()
    {
        $user = $this->service->get(['role' => 1]);

        $this->assertEquals(UserTestEntity::get(), $user);

        $role = $this->em->getRepository(Role::class)->findOneBy(['name' => 'admin']);
        $user = $this->service->get(['role' => $role]);

        $this->assertEquals(UserTestEntity::get(), $user);
    }

    public function testNotFindUser()
    {
        $user = $this->service->get(['login' => 'kek']);

        $this->assertNull($user);
    }

    public function testConvertToResponseObject()
    {
        $user = $this->service->get(['login' => 'test'], UserResponse::class);

        $this->assertEquals(UserTestResponse::get(), $user);
    }

    public function testGetUserList()
    {
        $users = $this->service->getIn(['login' => 'test']);

        $this->assertEquals([UserTestEntity::get()], $users);
    }

    public function testGetEmptyUserList()
    {
        $users = $this->service->getIn(['login' => 'kek']);

        $this->assertEquals([], $users);
    }

    public function testConvertToResponseObjectList()
    {
        $user = $this->service->getIn(['login' => 'test'], UserResponse::class);

        $this->assertEquals([UserTestResponse::get()], $user);
    }

    public function testCreateUser()
    {
        $user = $this->service->create(UserCreateArray::get());

        $userByDB = $this->em->getRepository(User::class)->findOneBy(['login' => 'my_user']);

        $this->assertEquals($user, $userByDB);
    }

    public function testCreateErrorByNotFullData()
    {
        $data = UserCreateArray::get();
        unset($data['password']);
        try {
            $this->service->create($data);
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testCreateWithNotUniqueData()
    {
        $data = UserCreateRequest::get();
        $data->login = 'test';
        try {
            $user = $this->service->create($data);
            $this->assertTrue(false);
        } catch (ValidationUniqueException $e) {
            $this->assertEquals('Параметр login не соответствует критериям уникальности.', $e->getMessage());
        }
    }

    public function testCreateByObject()
    {
        $user = $this->service->create(UserCreateRequest::get());

        $userByDB = $this->em->getRepository(User::class)->findOneBy(['login' => 'my_user']);

        $this->assertEquals($user, $userByDB);
    }

    public function testEdit()
    {
        $user = $this->service->edit(EditArray::get());

        $userByDB = $this->em->getRepository(User::class)->findOneBy(['login' => 'new']);

        $this->assertEquals($user, $userByDB);
    }

    public function testEditFromObject()
    {
        $user = $this->service->edit(EditObjectRequest::get());

        $userByDB = $this->em->getRepository(User::class)->findOneBy(['login' => 'new']);

        $this->assertEquals($user, $userByDB);
    }

    public function testEditWithNotUniqueData()
    {
        $data = EditObjectRequest::get();
        $data->login = "test1";

        try {
            $this->service->edit($data);
            $this->assertTrue(false);
        } catch (ValidationUniqueException $e) {
            $this->assertEquals("Параметр login не соответствует критериям уникальности.", $e->getMessage());
        }
    }

    public function testEditWithSelfData()
    {
        $data = EditObjectRequest::get();
        $data->login = "test";

        $user = $this->service->edit($data);

        $userByDB = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $this->assertEquals($user, $userByDB);
    }

    public function testEditWithoutId()
    {
        $data = EditArray::get();
        unset($data['id']);

        try {
            $this->service->edit($data);
            $this->assertTrue(false);
        } catch (IncorrectParamsException $e) {
            $this->assertEquals("Параметр id является обязательным", $e->getMessage());
        }
    }

    public function testEditNotFound()
    {
        $data = EditObjectRequest::get();
        $data->id = 5;

        try {
            $this->service->edit($data);
            $this->assertTrue(false);
        } catch (ObjectNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    public function testEditFromEntity()
    {
        /** @var User $userByDB */
        $userByDB = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);
        $userByDB->setPassword('new');

        /** @var User $result */
        $result = $this->service->edit($userByDB);

        $this->assertEquals("new", $result->getPassword());
    }

    public function testRemove()
    {
        $this->service->delete(1);

        /** @var User $userByDB */
        $userByDB = $this->em->getRepository(User::class)->findOneBy(['login' => 'test']);

        $this->assertEmpty($userByDB);
    }

    public function testRemoveNotFound()
    {
        try {
            $this->service->delete(5);
            $this->assertTrue(false);
        } catch (ObjectNotFoundException $e) {
            $this->assertTrue(true);
        }
    }
}