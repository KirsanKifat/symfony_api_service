<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\User;

class EditArray
{
    public static function get(): array
    {
        return [
            'id' => 1,
            'role' => 2,
            'login' => 'new'
        ];
    }
}