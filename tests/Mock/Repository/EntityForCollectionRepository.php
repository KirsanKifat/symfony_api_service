<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Repository;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\EntityForCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EntityForCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntityForCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntityForCollection[]    findAll()
 * @method EntityForCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntityForCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntityForCollection::class);
    }
}