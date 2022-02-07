<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

class UpdateUserRequest
{
    public string $login;

    public string $password;

    public ?string $email;

    public ?int $role;
}