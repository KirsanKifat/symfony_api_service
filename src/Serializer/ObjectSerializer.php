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
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function toArray(object $object, bool $returnNull = true, bool $returnOnlyInitNull = false): array
    {
        return $this->objectToArrayRecursive($object, $returnNull, $returnOnlyInitNull);
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
            return $this->setValueIntoObjectFromArrayRecursive($params, new $objectName(), $setNullValue);
        } elseif (is_object($params)) {
            if (get_class($params) === $objectName) {
                return $params;
            }
            $object = $this->setValueIntoObjectFromObjectRecursive($params, new $objectName(), $setNullValue);
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
            return $this->setValueIntoObjectFromArrayRecursive($params, $object, $setNullProperty);
        } else {
            return $this->setValueIntoObjectFromObjectRecursive($params, $object, $setNullProperty);
        }
    }

    private function setValueIntoObjectFromArrayRecursive(array $params, object $object, bool $setNullFlag): object
    {
        $reflectionClass = new \ReflectionClass($object);

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $reflectionProperty) {
            if (array_key_exists($reflectionProperty->getName(), $params)) {
                if (is_null($params[$reflectionProperty->getName()]) &&
                    (
                        $reflectionProperty->hasType() &&
                        !$reflectionProperty->getType()->allowsNull() ||
                        !$setNullFlag
                    )
                ) {
                    continue;
                }

                if (
                    $reflectionProperty->hasType() &&
                    class_exists($reflectionProperty->getType()->getName()) &&
                    !empty($params[$reflectionProperty->getName()]) &&
                    is_array($params[$reflectionProperty->getName()])
                ) {
                    $subClassName = $reflectionProperty->getType()->getName();
                    $params[$reflectionProperty->getName()] = $this->setValueIntoObjectFromArrayRecursive($params[$reflectionProperty->getName()], new $subClassName(), $setNullFlag);
                }

                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($object, $params[$reflectionProperty->getName()]);
            }
        }

        return $object;
    }

    private function setValueIntoObjectFromObjectRecursive(object $params, object $object, bool $setNullFlag, $recursive = false): object
    {
        $reflectionClassObject = new \ReflectionClass($object);
        $reflectionClassParams = new \ReflectionClass($params);

        if ($reflectionClassParams->hasMethod('__getLazyProperties')){
            $params->__load();
            $reflectionClassParams = $reflectionClassParams->getParentClass();
        }

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
                    $reflectionPropertyObject->hasType() &&
                    !$reflectionPropertyObject->getType()->allowsNull() ||
                    !$setNullFlag
                )
            ) {
                continue;
            }

            $subObjectClass = null;
            if ($reflectionPropertyObject->hasType()) {
                $subObjectClass = $reflectionPropertyObject->getType()->getName();
            }

            $reflectionPropertyObject->setAccessible(true);

            if (
                $reflectionPropertyObject->hasType() &&
                class_exists($subObjectClass) &&
                is_object($paramsValue) &&
                !get_class($paramsValue) instanceof $subObjectClass
            ) {
                $subObject = new $subObjectClass();
                if (
                    $reflectionPropertyObject->isInitialized($object) &&
                    !empty($reflectionPropertyObject->getValue($object))
                ) {
                    $subObject = $reflectionPropertyObject->getValue($object);
                }

                $paramsValue = $this->setValueIntoObjectFromObjectRecursive($paramsValue, $subObject, $setNullFlag, true);
            }

            $reflectionPropertyObject->setValue($object, $paramsValue);

        }

        return $object;
    }

    private function objectToArrayRecursive(object $object, bool $returnNull, bool $returnOnlyInitNull): array
    {
        $reflectionClass = new \ReflectionClass($object);

        if ($reflectionClass->hasMethod('__getLazyProperties')){
            $object->__load();
            $reflectionClass = $reflectionClass->getParentClass();
        }

        $reflectionProperties = $reflectionClass->getProperties();

        $resultArr = [];
        foreach ($reflectionProperties as $reflectionProperty) {
            $propertyName = $reflectionProperty->getName();
            $reflectionProperty->setAccessible(true);
            if ($reflectionProperty->isInitialized($object)) {
                if (!is_null($reflectionProperty->getValue($object))) {
                    if (is_object($reflectionProperty->getValue($object))) {
                        $resultArr[$propertyName] = $this->objectToArrayRecursive($reflectionProperty->getValue($object), $returnNull, $returnOnlyInitNull);
                    } else {
                        $resultArr[$propertyName] = $reflectionProperty->getValue($object);
                    }
                } elseif ($returnNull) {
                    $resultArr[$propertyName] = null;
                }
            } elseif ($returnNull && !$returnOnlyInitNull) {
                $resultArr[$propertyName] = null;
            }
        }

        return $resultArr;
    }
}