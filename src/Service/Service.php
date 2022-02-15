<?php

namespace KirsanKifat\ApiServiceBundle\Service;

use KirsanKifat\ApiServiceBundle\Exception\IncorrectParamsException;
use KirsanKifat\ApiServiceBundle\Exception\ObjectNotFoundException;
use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use KirsanKifat\ApiServiceBundle\Exception\ValidationUniqueException;
use KirsanKifat\ApiServiceBundle\Serializer\EntityObjectSerializer;
use KirsanKifat\ApiServiceBundle\Serializer\ObjectSerializer;
use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Serializer\ReflectionHelper;
use Psr\Log\LoggerInterface;

abstract class Service implements ServiceInterface
{
    protected EntityManagerInterface $em;
    protected LoggerInterface $logger;
    protected string $entityName;
    protected array $uniqueParams;
    protected ObjectSerializer $serializer;
    protected EntityObjectSerializer $entitySerializer;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, string $entityName, array $uniqueParams = [])
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->entityName = $entityName;
        $this->uniqueParams = $uniqueParams;
        $this->serializer = new ObjectSerializer($logger);
        $this->entitySerializer = new EntityObjectSerializer($em, $logger);
    }

    public function get($params, string $returnType = null): ?object
    {
        if (is_null($returnType)) {
            $returnType = $this->entityName;
        }

        if (is_object($params)) {
            $params = $this->serializer->toArray($params);
        }

        $entity = $this->em->getRepository($this->entityName)->findOneBy($params);

        if (!empty($entity)) {
            return $this->serializer->toObject($entity, $returnType);
        } else {
            return null;
        }
    }

    public function getIn($params, string $returnType = null): array
    {
        if (is_null($returnType)) {
            $returnType = $this->entityName;
        }

        if (is_object($params)) {
            $params = $this->serializer->toArray($params);
        }

        $entities = $this->em->getRepository($this->entityName)->findBy($params);

        foreach ($entities as $key => $entity) {
            $entities[$key] = $this->serializer->toObject($entity, $returnType);
        }

        return $entities;
    }

    public function create($params, string $returnType = null): object
    {
        if (is_object($params)) {
            $params = $this->serializer->toArray($params);
        }

        $this->checkUnique($params);

        if (is_null($returnType)) {
            $returnType = $this->entityName;
        }

        $entity = $this->entitySerializer->toEntity($params, $this->entityName);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->serializer->toObject($entity, $returnType);
    }

    public function edit($params, string $returnType = null): object
    {
        if (!is_object($params) || ReflectionHelper::getInitDoctrineProxyClass($params)->getName() !== $this->entityName) {
            if (is_object($params)) {
                $params = $this->serializer->toArray($params, false, true);
            }

            if (is_null($returnType)) {
                $returnType = $this->entityName;
            }

            if (!isset($params['id'])) {
                throw new IncorrectParamsException('Параметр id является обязательным');
            }

            $this->checkUnique($params, $params['id']);

            $entity = $this->em->getRepository($this->entityName)->find($params['id']);

            if (empty($entity)) {
                throw new ObjectNotFoundException();
            }

            $params = $this->entitySerializer->updateArray($params, $this->entityName);
            $entity = $this->serializer->updateObject($params, $entity);
        } else {
            $entity = $params;
        }
        $this->em->persist($entity);
        $this->em->flush();

        return $this->serializer->toObject($entity, $returnType);
    }

    public function delete(int $id): void
    {
        $entity = $this->em->getRepository($this->entityName)->find($id);

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
    protected function checkUnique(array $params, $idExclude = null): void
    {
        foreach ($params as $param => $value) {
            if (in_array($param, $this->uniqueParams)) {
                $entities = $this->em->getRepository($this->entityName)->findBy([$param => $value]);

                if (!empty($entities) &&
                    (
                        count($entities) > 1 ||
                        $entities[0]->getId() !== $idExclude
                    )
                ){
                    throw new ValidationUniqueException($param);
                }
            }
        }
    }
}