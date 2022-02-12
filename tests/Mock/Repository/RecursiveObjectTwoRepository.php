<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Repository;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RecursiveObjectTwo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecursiveObjectTwo|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecursiveObjectTwo|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecursiveObjectTwo[]    findAll()
 * @method RecursiveObjectTwo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecursiveObjectTwoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecursiveObjectTwo::class);
    }
}
