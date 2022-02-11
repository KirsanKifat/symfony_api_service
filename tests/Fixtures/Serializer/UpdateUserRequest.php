<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\RoleResponse;

class UpdateUserRequest
{
    public string $login;

    public string $password;

    public ?string $email;

    public ?RoleResponse $role;
}