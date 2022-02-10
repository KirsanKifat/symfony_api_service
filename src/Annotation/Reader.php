<?php

namespace KirsanKifat\ApiServiceBundle\Annotation;

use Doctrine\Common\Annotations\AnnotationReader as DocReader;

class Reader
{
    /**
     * @param $class
     * @return object[]
     */
    public static function getClass($class)
    {
        $reader = new DocReader;
        $reflector = new \ReflectionClass($class);
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
        $reflector = new \ReflectionProperty($class, $property);
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
        $reflector = new \ReflectionMethod($class, $method);
        return $reader->getMethodAnnotations($reflector);
    }

}