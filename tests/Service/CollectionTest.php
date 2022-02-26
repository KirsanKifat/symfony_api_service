<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\Logger;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\Collection\CreateByArray;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\EntityForCollection;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Entity\EntityWithCollection;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Service\CollectionService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CollectionTest extends KernelTestCase
{
    private CollectionService $service;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->service = new CollectionService($this->em, new Logger());
    }

    public function testGetByArray()
    {

    }

    public function testGetByObject()
    {

    }

    public function testGetByEntity()
    {

    }

    public function testGetListByArray()
    {

    }

    public function testGetListByObject()
    {

    }

    public function testGetListByEntity()
    {

    }

    /**
     * @return void
     * @group my
     */
    public function testCreateByArray()
    {
        /** @var EntityWithCollection $response */
        $response = $this->service->create(CreateByArray::get());

        $hasOneFlag = false;
        $hasThreeFlag = false;
        /** @var EntityForCollection $entityForCollection */
        foreach ($response->getCollection()->toArray() as $entityForCollection){
            if ($entityForCollection->getId() === 1) {
                $hasOneFlag = true;
            }

            if ($entityForCollection->getId() === 3) {
                $hasThreeFlag = true;
            }
        }

        $this->assertTrue($hasOneFlag);
        $this->assertTrue($hasThreeFlag);
    }

    public function testCreateByObject()
    {

    }

    public function testCreateByEntity()
    {

    }

    public function testEditByArray()
    {

    }

    public function testEditByObject()
    {

    }

    public function testEditByEntity()
    {

    }

    public function testDelete()
    {

    }
}