<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
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
        $reflectionClass = ReflectionHelper::getInitDoctrineProxyClass(new $objectName());

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $property) {
            if (isset($params[$property->getName()])) {
                $propertyType = ReflectionHelper::getPropertyType(new $objectName(), $property->getName());

                $params[$property->getName()] = $this->updateToEntity($params[$property->getName()], $propertyType);

                $params[$property->getName()] = $this->updateToCollection($params[$property->getName()], $property, $propertyType);
            }
        }

        return $params;
    }

    private function updateToEntity($value, string $propertyType)
    {
        if ($propertyType &&
            class_exists($propertyType) &&
            !$this->em->getMetadataFactory()->isTransient($propertyType) &&
            is_int($value)
        ) {
            $value = $this->em->getRepository($propertyType)->find($value);
        }

        return $value;
    }

    private function updateToCollection($value, \ReflectionProperty $property, string $propertyType)
    {
        if ($propertyType &&
            class_exists($propertyType) &&
            $propertyType === ArrayCollection::class &&
            is_array($value)
        ) {
            $defaultValue = $value;
            $value = new ArrayCollection();
            foreach ($defaultValue as $id) {
                if (!is_int($id)) {
                    $value = $defaultValue;
                    break;
                }

                $className = $property->getDeclaringClass()->getName();
                $collectionType = ReflectionHelper::getArrayCollectionPropertyTarget(new $className(),  $property->getName());
                $arrayCollObject = $this->em->getRepository($collectionType)->find($id);
                $value->add($arrayCollObject);
            }
        }

        return $value;
    }
}