<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Exception\ObjectNotFoundException;
use Psr\Log\LoggerInterface;
use ReflectionClass;

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
        return $this->updateObject($object);
    }

    private function updateArray(array $params, string $objectName): array
    {
        $reflectionClass = new \ReflectionClass($objectName);

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $property) {
            if ($property->hasType() &&
                class_exists($property->getType()->getName()) &&
                is_int($params[$property->getName()])
            ) {
                $params[$property->getName()] = ['id' => $params[$property->getName()]];
            }
        }

        return $params;
    }

    private function updateObject(object $object): object
    {
        $reflectionClass = new ReflectionClass($object);

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            if (!$reflectionProperty->hasType() ||
                !$reflectionProperty->isInitialized($object) ||
                empty($reflectionProperty->getValue($object))
            ) {
                continue;
            }

            $className = $reflectionProperty->getType()->getName();
            $propertyValue = $reflectionProperty->getValue($object);
            if (
                class_exists($className) &&
                !$this->em->getMetadataFactory()->isTransient($className) &&
                $reflectionClass->hasMethod('getId')
            ) {
                $entity = $this->em->getRepository($className)->find($propertyValue->getId());
                if (empty($entity)) {
                    throw new ObjectNotFoundException('Объект класса ' . $className .
                        ' с id=' . $propertyValue->getId() . ' не найден');
                }

                $reflectionProperty->setValue($object, $entity);
            }
        }

        return $object;
    }
}