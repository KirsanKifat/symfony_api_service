<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Repository;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithAnnotation;

/**
 * @method UserWithAnnotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWithAnnotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWithAnnotation[]    findAll()
 * @method UserWithAnnotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepositoryWithAnnotation extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWithAnnotation::class);
    }
}