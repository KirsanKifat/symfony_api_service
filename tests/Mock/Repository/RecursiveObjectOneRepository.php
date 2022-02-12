<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Repository;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursiveObjectOne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecursiveObjectOne|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecursiveObjectOne|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecursiveObjectOne[]    findAll()
 * @method RecursiveObjectOne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecursiveObjectOneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecursiveObjectOne::class);
    }
}
