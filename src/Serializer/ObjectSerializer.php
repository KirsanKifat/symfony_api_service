<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use Psr\Log\LoggerInterface;

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
        if (!is_object($params) && !is_array($params)) {
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
        $reflectionClass = ReflectionHelper::getInitDoctrineProxyClass($object);

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

                $type = ReflectionHelper::getPropertyType($object, $propertyName);

                $reflectionProperty->setAccessible(true);

                if (
                    !is_null($type) &&
                    class_exists($type) &&
                    !empty($params[$propertyName]) &&
                    is_array($params[$propertyName])
                ) {
                    if ($reflectionProperty->isInitialized($object) &&
                        !is_null($reflectionProperty->getValue($object))
                    ) {
                        $params[$propertyName] = $this->setValueIntoObjectFromArrayRecursive($params[$propertyName], $reflectionProperty->getValue($object), $setNullFlag);
                    } else {
                        $params[$propertyName] = $this->setValueIntoObjectFromArrayRecursive($params[$propertyName], new $type(), $setNullFlag);
                    }
                }

                $reflectionProperty->setValue($object, $params[$propertyName]);
            }
        }

        return $object;
    }

    private function setValueIntoObjectFromObjectRecursive(object $params, object $object, bool $setNullFlag, $recursive = false): object
    {
        $reflectionClassObject = ReflectionHelper::getInitDoctrineProxyClass($object);
        $reflectionClassParams = ReflectionHelper::getInitDoctrineProxyClass($params);

        // В случае если объект является прокси объектом (а это только в случае если это не 1 уровня объект)
        // и объект для обновления является тем же классом (оба объекта сравниваются не по прокси классам а по родительским)
        // возвращаем полностью замененный объект т.к. он с привязкой к базе данных
        if ($reflectionClassParams->getName() === $reflectionClassObject->getName() &&
            ReflectionHelper::checkDoctrineProxyClass($object)
        ) {
            return $params;
        }

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

            $type = ReflectionHelper::getPropertyType($object, $propertyObjectName);

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
        $reflectionClass = ReflectionHelper::getInitDoctrineProxyClass($object);

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