<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Repository;

use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\EntityWithCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EntityWithCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntityWithCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntityWithCollection[]    findAll()
 * @method EntityWithCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntityWithCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntityWithCollection::class);
    }
}