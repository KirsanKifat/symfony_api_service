<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Log\LoggerInterface;

class ObjectSerializer
{
    private Serializer $serializer;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $builder = SerializerBuilder::create();
        $builder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
        $this->serializer = $builder->build();
    }

    public function toArray(object $object, $returnNull = true, $returnOnlyInitNull = false): array
    {
        [$object, $initNotNullProperties] = $this->recursiveInitializationAnyProperty($object);
        if (!$returnNull && $returnOnlyInitNull) {
            $array = $this->serializer->toArray($object, SerializationContext::create()->enableMaxDepthChecks()->setSerializeNull(true));
        } else {
            $array = $this->serializer->toArray($object, SerializationContext::create()->enableMaxDepthChecks()->setSerializeNull($returnNull));
        }

        if ($returnNull) {
            return $this->recursiveSetNullForInitProperty($array,$initNotNullProperties);
        } else {
            return $this->recursiveRemoveNullKeysForInitProperty($array, $initNotNullProperties);
        }
    }

    /**
     * Converted to object from object or array
     *
     * @param array|object $params
     * @param string $objectName
     * @return object
     */
    public function toObject($params, string $objectName, $setNullValue = true): object
    {
        if (!class_exists($objectName)) {
            $this->logger->error("Класс " . $objectName . " не существует");
            throw new ServerException();
        }

        if (is_array($params)) {
            if ($setNullValue) {
                $params = $this->recursiveClearArrayForDeserialization($params, $objectName);
            } else {
                $params = $this->recursiveRemoveNullValue($params);
            }

            $object = $this->serializer->fromArray($params, $objectName);
        } elseif (is_object($params)) {
            $params = $this->toArray($params, $setNullValue);
            $object = $this->toObject($params, $objectName, $setNullValue);
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
    public function updateObject($params, object $object, bool $setNullProperty = true): object
    {
        if (!is_object($params) && !is_array($params)){
            $this->logger->error("Параметр params дожен быть массивом или объектом, получен тип " . gettype($params));
            throw new ServerException();
        }

        if (is_array($params)) {
            return $this->setValueIntoObjectFromArray($params, $object, $setNullProperty);
        } else {
            return $this->setValueIntoObjectFromObject($params, $object, $setNullProperty);
        }
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

    private function recursiveRemoveNullKeysForInitProperty(array $array, array $initNotNullProperties): array
    {
        foreach ($initNotNullProperties as $key => $initNotNullProperty) {
            if (is_array($initNotNullProperty) && !empty($initNotNullProperty)) {
                $array[$key] = $this->recursiveRemoveNullKeysForInitProperty($array[$key], $initNotNullProperty);
            } else {
                unset($array[$initNotNullProperty]);
            }
        }

        return $array;
    }

    private function recursiveClearArrayForDeserialization($params, $objectName)
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
                $params[$propertyName] = $this->recursiveClearArrayForDeserialization($params[$propertyName], $reflectionProperty->getType()->getName());
            }
        }

        return $params;
    }

    private function recursiveRemoveNullValue(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $array[$key] = $this->recursiveRemoveNullValue($value);
            } elseif (is_null($value)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    private function setValueIntoObjectFromArray(array $params, object $object, bool $setNullFlag): object
    {
        $reflectionClass = new \ReflectionClass($object);

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $reflectionProperty) {
            if (array_key_exists($reflectionProperty->getName(), $params)) {
                if (is_null($params[$reflectionProperty->getName()]) &&
                    (
                        !$reflectionProperty->getType()->allowsNull() ||
                        !$setNullFlag
                    )
                ) {
                    continue;
                }

                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($object, $params[$reflectionProperty->getName()]);
            }
        }

        return $object;
    }

    private function setValueIntoObjectFromObject(object $params, object $object, bool $setNullFlag): object
    {
        $reflectionClassObject = new \ReflectionClass($object);
        $reflectionClassParams = new \ReflectionClass($params);

        $reflectionPropertiesObject = $reflectionClassObject->getProperties();

        foreach ($reflectionPropertiesObject as $reflectionPropertyObject) {
            if (!$reflectionClassParams->hasProperty($reflectionPropertyObject->getName())) {
                continue;
            }

            $paramsProperty = $reflectionClassParams->getProperty($reflectionPropertyObject->getName());
            $paramsProperty->setAccessible(true);

            if (!$paramsProperty->isInitialized($params)) {
                continue;
            }

            $paramsValue = $paramsProperty->getValue($params);
            if (is_null($paramsValue) &&
                (
                    !$reflectionPropertyObject->getType()->allowsNull() ||
                    !$setNullFlag
                )
            ) {
                continue;
            }

            $reflectionPropertyObject->setAccessible(true);
            $reflectionPropertyObject->setValue($object, $paramsValue);

        }

        return $object;
    }
}