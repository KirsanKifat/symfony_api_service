<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Dto;

class UserResponse
{
    public int $id;

    public string $login;

    public string $email;

    public RoleResponse $role;
}