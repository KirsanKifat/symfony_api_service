<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Annotation;

use JMS\Serializer\Annotation\Type;
use KirsanKifat\ApiServiceBundle\Annotation\Reader;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\Role;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\RoleWithAnnotation;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\UserWithAnnotation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReaderTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testReadClass()
    {
        $propertyAnnotations = Reader::getProperty(UserWithAnnotation::class, 'role');

        /** @var Type $jsmType */
        $jsmType = null;
        foreach ($propertyAnnotations as $annotation) {
            if ($annotation instanceof Type) {
                $jsmType = $annotation;
            }
        }

        $this->assertNotNull($jsmType);

        $this->assertEquals($jsmType->name, RoleWithAnnotation::class);
    }
}