<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Dto;

class EditRequest
{
    public int $id;

    public string $login;

    public string $password;

    public string $email;

    public int $role;

    public bool $active;
}