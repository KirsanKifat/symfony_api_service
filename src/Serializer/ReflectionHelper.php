<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;

use JMS\Serializer\Annotation\Type;
use KirsanKifat\ApiServiceBundle\Annotation\Reader;
use ReflectionClass;

class ReflectionHelper
{
    public static function getPropertyType(object $object, string $propertyName): ?string
    {
        $reflectionClass = self::getInitDoctrineProxyClass($object);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);


        $propertyType = null;
        if ($reflectionProperty->hasType()) {
            $propertyType = $reflectionProperty->getType()->getName();
        } else {
            $propertyAnnotations = Reader::getProperty($object, $propertyName);

            foreach ($propertyAnnotations as $annotation) {
                if ($annotation instanceof Type) {
                    $propertyType = $annotation->name;
                }
            }
        }

        return $propertyType;
    }

    public static function getInitDoctrineProxyClass(object $object): ReflectionClass
    {
        $reflectionClass = new ReflectionClass($object);
        if ($reflectionClass->hasMethod('__getLazyProperties')) {
            $object->__load();
            $reflectionClass = $reflectionClass->getParentClass();
        }

        return $reflectionClass;
    }

    public static function checkDoctrineProxyClass(object $object): bool
    {
        $reflectionClass = new ReflectionClass($object);
        if ($reflectionClass->hasMethod('__getLazyProperties')) {
            return true;
        }

        return false;
    }
}