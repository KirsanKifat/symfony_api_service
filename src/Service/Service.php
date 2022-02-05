<?php

namespace KirsanKifat\ApiServiceBundle\Service;

use KirsanKifat\ApiServiceBundle\Exception\ObjectNotFoundException;
use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use KirsanKifat\ApiServiceBundle\Exception\ValidationUniqueException;
use KirsanKifat\ApiServiceBundle\Serializer\EntityObjectSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class Service implements ServiceInterface
{
    protected EntityManagerInterface $em;
    protected LoggerInterface $logger;
    protected string $entityName;
    protected array $uniqueParams;
    protected EntityObjectSerializer $serializer;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, string $entityName, array $uniqueParams = [])
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->entityName = $entityName;
        $this->uniqueParams = $uniqueParams;
        $this->serializer = new EntityObjectSerializer();
    }

    public function get($params, string $returnType): object
    {
        $params = $this->serializer->toArray($params);

        $entity = $this->em->getRepository($this->entityName)->findOneBy($params);

        return $this->serializer->toObject($entity, $returnType);
    }

    public function getIn($params, string $returnType): array
    {
        $params = $this->serializer->toArray($params);

        $entities = $this->em->getRepository($this->entityName)->findBy($params);

        foreach ($entities as $key => $entity) {
            $entities[$key] = $this->serializer->toObject($entity, $returnType);
        }

        return $entities;
    }

    public function create($params, string $returnType): object
    {
        $this->checkUnique($this->serializer->toArray($params));

        $entity = $this->serializer->toObject($params, $this->entityName);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->serializer->toObject($entity, $returnType);
    }

    public function edit($params, string $returnType): object
    {
        $params = $this->serializer->toArray($params);

        if (!isset($params['id'])) {
            throw new ServerException();
        }

        $this->checkUnique($params);

        $entity = $this->em->getRepository($this->entityName)->find($params['id']);

        if (empty($entity)) {
            throw new ObjectNotFoundException();
        }

        $entity = $this->serializer->updateObject($params, $entity);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->serializer->toObject($entity, $returnType);
    }

    public function delete($params): void
    {
        $params = $this->serializer->toArray($params);

        if (!isset($params['id'])) {
            throw new ServerException();
        }

        $entity = $this->em->getRepository($this->entityName)->find($params['id']);

        if (empty($entity)) {
            throw new ObjectNotFoundException();
        }

        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @param array $params
     * @return void
     * @throws ValidationUniqueException
     */
    protected function checkUnique(array $params): void
    {
        foreach ($params as $param => $value) {
            if (in_array($param, $this->uniqueParams)) {
                $entity = $this->em->getRepository($this->entityName)->findOneBy([$param => $value]);

                if (!empty($entity)){
                    throw new ValidationUniqueException($param);
                }
            }
        }
    }
}