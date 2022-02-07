<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;

class UpdateUserRequest
{
    public string $login;

    public string $password;

    public string $email;

    public int $role;
}