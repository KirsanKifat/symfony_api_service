<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures;

class UpdateUserObject
{
    public static function get(): UpdateUserRequest
    {
        $request = new UpdateUserRequest();

        $request->login = 'new';
        $request->email = 'my_email@gmail.com';

        return $request;
    }
}