<?php

namespace KirsanKifat\ApiServiceBundle\Annotation;

use Doctrine\Common\Annotations\AnnotationReader as DocReader;
use KirsanKifat\ApiServiceBundle\Serializer\ReflectionHelper;

class Reader
{
    /**
     * @param $class
     * @return object[]
     */
    public static function getClass($class)
    {
        $reader = new DocReader;
        $reflector = ReflectionHelper::getInitDoctrineProxyClass($class);
        return $reader->getClassAnnotations($reflector);
    }

    /**
     * @param $class
     * @param $property
     * @return array
     */
    public static function getProperty($class, $property): array
    {
        $reader = new DocReader;
        $reflectionClass = ReflectionHelper::getInitDoctrineProxyClass($class);
        $reflector = $reflectionClass->getProperty($property);
        return $reader->getPropertyAnnotations($reflector);
    }

    /**
     * @param $class
     * @param $method
     * @return array
     */
    public static function getMethod($class, $method): array
    {
        $reader = new DocReader;
        $reflectionClass = ReflectionHelper::getInitDoctrineProxyClass($class);
        $reflector = $reflectionClass->getMethod($method);
        return $reader->getMethodAnnotations($reflector);
    }

}