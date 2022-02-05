<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use Psr\Log\LoggerInterface;

class EntityObjectSerializer
{
    private Serializer $serializer;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function toArray(object $object): array
    {
        [$object, $initNotNullProperties] = $this->recursiveInitializationAnyProperty($object);
        $array = $this->serializer->toArray($object, SerializationContext::create()->enableMaxDepthChecks());

        return $this->recursiveSetNullForInitProperty($array,$initNotNullProperties);
    }

    /**
     * Converted to object from object or array
     *
     * @param array|object $params
     * @param string $objectName
     * @return object
     */
    public function toObject($params, string $objectName): object
    {
        if (!class_exists($objectName)) {
            $this->logger->error("Класс " . $objectName . " не существует");
            throw new ServerException();
        }

        if (is_array($params)) {
            $params = $this->recursiveRemoveNullArrayValue($params, $objectName);
            $object = $this->serializer->fromArray($params, $objectName);
        } elseif (is_object($params)) {
            $params = $this->toArray($params);
            $object = $this->toObject($params, $objectName);
        } else {
            $this->logger->error("Параметр params дожен быть массивом или объектом, получен тип " . gettype($params));
            throw new ServerException();
        }

        return $object;
    }

    /**
     * @param array|object $params
     * @param object $object
     * @return object
     */
    public function updateObject($params, object $object): object
    {

    }

    private function recursiveInitializationAnyProperty(object $object): array
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperties = $reflectionClass->getProperties();
        $initNotNullProperties = [];
        foreach ($reflectionProperties as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            if (!$reflectionProperty->isInitialized($object)) {
                if ($reflectionProperty->hasType() && !$reflectionProperty->getType()->allowsNull()) {
                    if (
                        $reflectionProperty->getType()->getName() === 'int' ||
                        $reflectionProperty->getType()->getName() === 'float'
                    ) {
                        $reflectionProperty->setValue($object, 0);
                    } elseif ($reflectionProperty->getType()->getName() === 'string') {
                        $reflectionProperty->setValue($object, '');
                    } elseif ($reflectionProperty->getType()->getName() === 'array') {
                        $reflectionProperty->setValue($object, []);
                    } elseif ($reflectionProperty->getType()->getName() === 'bool') {
                        $reflectionProperty->setValue($object, false);
                    } elseif (class_exists($reflectionProperty->getType()->getName())) {
                        $type = $reflectionProperty->getType()->getName();
                        if (get_class($object) === $type) {
                            $this->logger->error('Объект ' . get_class($object) . ' имеет в себе рекурсивное представление себя');
                            throw new ServerException();
                        }

                        [$subObject, $trash] = $this->recursiveInitializationAnyProperty(new $type());

                        $reflectionProperty->setValue($object, $subObject);
                    }
                    $initNotNullProperties[] = $reflectionProperty->getName();
                } else {
                    $reflectionProperty->setValue($object,null);
                }
            } else {
                if (is_object($reflectionProperty->getValue($object))) {
                    [$subObject, $subInitNotNullProperties] = $this->recursiveInitializationAnyProperty($reflectionProperty->getValue($object));
                    if (!empty($subInitNotNullProperties)) {
                        $reflectionProperty->setValue($object, $subObject);
                        $initNotNullProperties[$reflectionProperty->getName()] = $subInitNotNullProperties;
                    }
                }
            }
        }

        return [$object, $initNotNullProperties];
    }

    private function recursiveSetNullForInitProperty(array $array, array $initNotNullProperties): array
    {
        foreach ($initNotNullProperties as $key => $initNotNullProperty) {
            if (is_array($initNotNullProperty) && !empty($initNotNullProperty)) {
                $array[$key] = $this->recursiveSetNullForInitProperty($array[$key], $initNotNullProperty);
            } else {
                $array[$initNotNullProperty] = null;
            }
        }

        return $array;
    }

    public function recursiveRemoveNullArrayValue($params, $objectName)
    {
        $reflectionClass = new \ReflectionClass($objectName);

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $reflectionProperty) {
            $propertyName = $reflectionProperty->getName();
            if(
                $reflectionProperty->hasType() &&
                !$reflectionProperty->getType()->allowsNull() &&
                empty($params[$propertyName])
            ) {
                unset($params[$propertyName]);
            }

            if (
                $reflectionProperty->hasType() &&
                class_exists($reflectionProperty->getType()->getName()) &&
                is_array($params[$propertyName])
            ) {
                $params[$propertyName] = $this->recursiveRemoveNullArrayValue($params[$propertyName], $reflectionProperty->getType()->getName());
            }
        }

        return $params;
    }
}