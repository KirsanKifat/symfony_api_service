<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Repository;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RoleWithAnnotation;

/**
 * @method RoleWithAnnotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoleWithAnnotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoleWithAnnotation[]    findAll()
 * @method RoleWithAnnotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleWithAnnotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleWithAnnotation::class);
    }
}
