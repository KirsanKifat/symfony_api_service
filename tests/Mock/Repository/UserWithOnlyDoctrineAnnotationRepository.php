<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithOnlyDoctrineAnnotation;

/**
 * @method UserWIthOnlyDoctrineAnnotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWIthOnlyDoctrineAnnotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWIthOnlyDoctrineAnnotation[]    findAll()
 * @method UserWIthOnlyDoctrineAnnotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWithOnlyDoctrineAnnotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWIthOnlyDoctrineAnnotation::class);
    }
}