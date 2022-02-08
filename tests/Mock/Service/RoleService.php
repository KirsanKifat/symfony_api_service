<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Service;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Service\Service;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use Psr\Log\LoggerInterface;

class RoleService extends Service
{
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        parent::__construct($em, $logger, Role::class, ['name']);
    }
}