<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KirsanKifat\ApiServiceBundle\Annotation\Reader;
use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class ObjectSerializer
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param object $object
     * @param bool $returnNull
     * @param bool $returnOnlyInitNull
     * @return array
     */
    public function toArray(object $object, bool $returnNull = true, bool $returnOnlyInitNull = false): array
    {
        return $this->objectToArrayRecursive($object, $returnNull, $returnOnlyInitNull);
    }

    /**
     * Converted to object from object or array
     *
     * @param array|object $params
     * @param string $objectName
     * @param bool $setNullValue
     * @return object
     * @throws ServerException
     */
    public function toObject($params, string $objectName, bool $setNullValue = true): object
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
     * @param bool $setNullProperty
     * @return object
     * @throws ServerException
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
            $propertyName = $reflectionProperty->getName();
            if (array_key_exists($propertyName, $params)) {
                if (is_null($params[$propertyName]) &&
                    (
                        $reflectionProperty->hasType() &&
                        !$reflectionProperty->getType()->allowsNull() ||
                        !$setNullFlag
                    )
                ) {
                    continue;
                }

                $type = null;
                if ($reflectionProperty->hasType()) {
                    $type = $reflectionProperty->getType()->getName();
                } elseif (!$reflectionProperty->hasType() &&
                    $this->getTypeByJsmAnnotation($object, $propertyName)
                ) {
                    $type = $this->getTypeByJsmAnnotation($object, $propertyName);
                }

                if (
                    !is_null($type) &&
                    class_exists($type) &&
                    !empty($params[$propertyName]) &&
                    is_array($params[$propertyName])
                ) {
                    $params[$propertyName] = $this->setValueIntoObjectFromArrayRecursive($params[$propertyName], new $type(), $setNullFlag);
                }

                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($object, $params[$propertyName]);
            }
        }

        return $object;
    }

    private function setValueIntoObjectFromObjectRecursive(object $params, object $object, bool $setNullFlag, $recursive = false): object
    {
        $reflectionClassObject = new ReflectionClass($object);
        $reflectionClassParams = $this->getInitDoctrineReflectionClass($params);

        $reflectionPropertiesObject = $reflectionClassObject->getProperties();

        foreach ($reflectionPropertiesObject as $reflectionPropertyObject) {
            $propertyObjectName = $reflectionPropertyObject->getName();
            if (!$reflectionClassParams->hasProperty($propertyObjectName)) {
                continue;
            }

            $paramsProperty = $reflectionClassParams->getProperty($propertyObjectName);
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

            $type = null;
            if ($reflectionPropertyObject->hasType()) {
                $type = $reflectionPropertyObject->getType()->getName();
            } elseif (!$reflectionPropertyObject->hasType() &&
                $this->getTypeByJsmAnnotation($object, $propertyObjectName)
            ) {
                $type = $this->getTypeByJsmAnnotation($object, $propertyObjectName);
            }

            $reflectionPropertyObject->setAccessible(true);

            if (
                !is_null($type) &&
                class_exists($type) &&
                is_object($paramsValue) &&
                !get_class($paramsValue) instanceof $type
            ) {
                $subObject = new $type();
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
        $reflectionClass = $this->getInitDoctrineReflectionClass($object);

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

    private function getTypeByJsmAnnotation(object $object, string $propertyName): ?string
    {
        $propertyAnnotations = Reader::getProperty($object, $propertyName);

        /** @var Type $jsmType */
        $jsmType = null;
        foreach ($propertyAnnotations as $annotation) {
            if ($annotation instanceof Type) {
                $jsmType = $annotation->name;
            }
        }

        return $jsmType;
    }

    private function getInitDoctrineReflectionClass(object $object): ReflectionClass
    {
        $reflectionClass = new ReflectionClass($object);
        if ($reflectionClass->hasMethod('__getLazyProperties')){
            $object->__load();
            $reflectionClass = $reflectionClass->getParentClass();
        }

        return $reflectionClass;
    }
}