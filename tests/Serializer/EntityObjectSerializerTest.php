<?php

namespace App\Tests\Serializer;

use App\Tests\Mock\Entity\Role;
use App\Tests\Mock\Entity\User;
use KirsanKifat\ApiServiceBundle\Serializer\EntityObjectSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityObjectSerializerTest extends KernelTestCase
{
    private EntityObjectSerializer $serializer;
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->user->setLogin("test");
        $this->user->setPassword("saidighgjh");
        $this->user->setEmail("test@gmail.com");
        $role = new Role();
        $role->setName("admin");
        $this->user->setRole($role);
        $this->user->setActive(true);

        $this->serializer = new EntityObjectSerializer();
    }

    public function testToArray()
    {
        $array = $this->serializer->toArray($this->user);

        $this->assertEquals($array, [
            'id' => null,
            'login' => 'test',
            'password' => 'saidighgjh',
            'email' => 'test@gmail.com',
            'role' => ['id' => null, 'name' => 'admin'],
            'active' => true
        ]);
    }
}