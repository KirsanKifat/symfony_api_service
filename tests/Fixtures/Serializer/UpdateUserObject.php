<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\RoleResponse;

class UpdateUserObject
{
    public static function get(): UpdateUserRequest
    {
        $request = new UpdateUserRequest();

        $request->login = 'new';
        $request->email = 'my_email@gmail.com';
        $request->role = new RoleResponse();
        $request->role->name = 'user';

        return $request;
    }
}