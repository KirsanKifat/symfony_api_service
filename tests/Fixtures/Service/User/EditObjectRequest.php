<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Dto\EditRequest;

class EditObjectRequest
{
    public static function get(): EditRequest
    {
        $obj = new EditRequest();

        $obj->id = 1;
        $obj->role = 2;
        $obj->login = "new";

        return $obj;
    }
}