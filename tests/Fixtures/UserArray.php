<?php
namespace KirsanKifat\ApiServiceBundle\Tests\Fixtures;

class UserArray {
    public static function get(): array
    {
        return [
            'id' => null,
            'login' => 'test',
            'password' => 'test',
            'email' => 'test@gmail.com',
            'role' => ['id' => null, 'name' => 'admin'],
            'active' => true
        ];
    }
}
