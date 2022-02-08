<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Service;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Service\Service;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;
use Psr\Log\LoggerInterface;

class UserService extends Service
{
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        parent::__construct($em, $logger, User::class, ['login']);
    }
}