<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RoleWIthOnlyDoctrineAnnotation;

/**
 * @method RoleWIthOnlyDoctrineAnnotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoleWIthOnlyDoctrineAnnotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoleWIthOnlyDoctrineAnnotation[]    findAll()
 * @method RoleWIthOnlyDoctrineAnnotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleWithOnlyDoctrineAnnotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleWIthOnlyDoctrineAnnotation::class);
    }
}