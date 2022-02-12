<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class EntityObjectSerializer
{
    private EntityManagerInterface $em;

    private ObjectSerializer $serializer;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->serializer = new ObjectSerializer($logger);
    }

    public function toEntity(array $params, string $entityName)
    {
        $params = $this->updateArray($params, $entityName);
        $object = $this->serializer->toObject($params, $entityName);
        return $object;
    }

    public function updateArray(array $params, string $objectName): array
    {
        $reflectionClass = new \ReflectionClass($objectName);

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $property) {
            $propertyType = ReflectionHelper::getPropertyType(new $objectName(),  $property->getName());
            if ($propertyType &&
                class_exists($propertyType) &&
                isset($params[$property->getName()]) &&
                is_int($params[$property->getName()])
            ) {
                $params[$property->getName()] = $this->em->getRepository($propertyType)->find($params[$property->getName()]);
            }
        }

        return $params;
    }
}