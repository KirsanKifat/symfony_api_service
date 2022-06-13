<?php

namespace KirsanKifat\ApiServiceBundle\Serializer;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use JMS\Serializer\Annotation\Type;
use KirsanKifat\ApiServiceBundle\Annotation\Reader;
use KirsanKifat\ApiServiceBundle\Exception\ServerException;
use ReflectionClass;

class ReflectionHelper
{
    /**
     * Получает значение типа по алгоритму:
     * из типа переменной
     * Из типа jsm serializer
     * из аннотации doctrine
     *
     * @param object $object
     * @param string $propertyName
     * @return string|null
     * @throws \ReflectionException
     */
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
                } elseif ($annotation instanceof ManyToMany) {
                    $propertyType = ArrayCollection::class;
                } elseif (
                    $annotation instanceof OneToMany ||
                    $annotation instanceof ManyToOne ||
                    $annotation instanceof OneToOne
                ) {
                    $propertyType = $annotation->targetEntity;
                } elseif ($annotation instanceof Column) {
                    $propertyType = $annotation->type;
                }
            }
        }

        return $propertyType;
    }

    /**
     * Функция получает тип объектов в коллекции
     *
     * @param object $object
     * @param string $propertyName
     * @return string|null
     * @throws ServerException
     */
    public static function getArrayCollectionPropertyTarget(object $object, string $propertyName): ?string
    {
        $propertyType = null;

        $propertyAnnotations = Reader::getProperty($object, $propertyName);

        foreach ($propertyAnnotations as $annotation) {
            if ($annotation instanceof ManyToMany) {
                $propertyType = $annotation->targetEntity;
            }
        }

        if (is_null($propertyType)) {
            throw new ServerException();
        }

        return $propertyType;
    }

    /**
     * Получает ReflecitionClass, если это объект doctrine с ленивой загрузкой, получает инициализированный класс
     *
     * @param object|string $object
     * @return ReflectionClass
     */
    public static function getInitDoctrineProxyClass($object): ReflectionClass
    {
        $reflectionClass = new ReflectionClass($object);
        if ($reflectionClass->hasMethod('__getLazyProperties')) {
            $object->__load();
            $reflectionClass = $reflectionClass->getParentClass();
        }

        return $reflectionClass;
    }

    /**
     * Проверяем является ли обхектом doctrine с ленивой загрузкой
     *
     * @param object $object
     * @return bool
     */
    public static function checkDoctrineProxyClass(object $object): bool
    {
        $reflectionClass = new ReflectionClass($object);
        if ($reflectionClass->hasMethod('__getLazyProperties')) {
            return true;
        }

        return false;
    }
}