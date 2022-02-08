<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Dto;

class CreateRequest
{
    public string $login;

    public string $password;

    public string $email;

    public int $role;

    public bool $active;
}