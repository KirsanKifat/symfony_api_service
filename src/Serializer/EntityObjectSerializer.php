<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class EntityObjectSerializer
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function toArray(object $object): array
    {
        [$object, $initNotNullProperties] = $this->initializationAnyProperty($object);
        $array = $this->serializer->toArray($object, SerializationContext::create()->enableMaxDepthChecks());

        foreach ($initNotNullProperties as $initNotNullProperty) {
            $array[$initNotNullProperty] = null;
        }

        return $array;
    }

    /**
     * @param array|object $params
     * @param string $objectName
     * @return object
     */
    public function toObject($params, string $objectName): object
    {

    }

    /**
     * @param object $object
     * @param array|object $params
     * @return object
     */
    public function updateObject($params, object $object): object
    {

    }

    private function initializationAnyProperty(object $object): array
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperties = $reflectionClass->getProperties();
        $initNotNullProperties = [];
        foreach ($reflectionProperties as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            if (!$reflectionProperty->isInitialized($object)) {
                if ($reflectionProperty->hasType()) {
                    if ($reflectionProperty->getType()->getName() === 'int') {
                        $reflectionProperty->setValue($object, 0);
                    } elseif ($reflectionProperty->getType()->getName() === 'string') {
                        $reflectionProperty->setValue($object, '');
                    } elseif ($reflectionProperty->getType()->getName() === 'array') {
                        $reflectionProperty->setValue($object, []);
                    } elseif (is_object($reflectionProperty->getType()->getName())) {
                        $type = $reflectionProperty->getType()->getName();
                        $reflectionProperty->setValue($object, new $type());
                    }
                    $initNotNullProperties[] = $reflectionProperty->getName();
                } else {
                    $reflectionProperty->setValue($object,null);
                }
            }
        }

        return [$object, $initNotNullProperties];
    }
}