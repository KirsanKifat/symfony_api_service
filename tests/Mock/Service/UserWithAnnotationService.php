<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Service;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Service\Service;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithAnnotation;
use Psr\Log\LoggerInterface;

class UserWithAnnotationService extends Service
{
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        parent::__construct($em, $logger, UserWithAnnotation::class, ['login']);
    }
}