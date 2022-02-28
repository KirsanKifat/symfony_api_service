<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Serializer\Logger;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\Collection\CreateByArray;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\Collection\CreateByObject;
use KirsanKifat\ApiServiceBundle\Tests\Fixtures\Service\Collection\EditByObject;
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
        /** @var EntityWithCollection $response */
        $response = $this->service->create(CreateByObject::get());

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

    public function testCreateByEntity()
    {
        $object = new EntityWithCollection();
        $object->setName('test');
        $collObj1 = $this->em->getRepository(EntityForCollection::class)->find(1);
        $collObj3 = $this->em->getRepository(EntityForCollection::class)->find(3);
        $object->addCollection($collObj1);
        $object->addCollection($collObj3);

        /** @var EntityWithCollection $response */
        $response = $this->service->create($object);

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

    public function testCreateWithArrayOfEntityToCollection()
    {
        $collObj1 = $this->em->getRepository(EntityForCollection::class)->find(1);
        $collObj3 = $this->em->getRepository(EntityForCollection::class)->find(3);
        $array = CreateByArray::get();
        $array['collection'] = [$collObj1, $collObj3];
        $response = $this->service->create($array);

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

    public function testEditByArray()
    {
        /** @var EntityWithCollection $response */
        $response = $this->service->create(CreateByArray::get());
        $arrayEdit = [
            'id' => $response->getId(),
            'collection' => [1, 2]
        ];
        $entity = $this->service->edit($arrayEdit);

        $hasOneFlag = false;
        $hasTwoFlag = false;
        $hasThreeFlag = false;
        /** @var EntityForCollection $entityForCollection */
        foreach ($entity->getCollection()->toArray() as $entityForCollection){
            if ($entityForCollection->getId() === 1) {
                $hasOneFlag = true;
            }

            if ($entityForCollection->getId() === 2) {
                $hasTwoFlag = true;
            }

            if ($entityForCollection->getId() === 3) {
                $hasThreeFlag = true;
            }
        }

        $this->assertTrue($hasOneFlag);
        $this->assertTrue($hasTwoFlag);
        $this->assertFalse($hasThreeFlag);
    }

    public function testEditByObject()
    {
        /** @var EntityWithCollection $response */
        $response = $this->service->create(CreateByArray::get());
        $editObject = EditByObject::get();

        $editObject->id = $response->getId();

        $entity = $this->service->edit($editObject);

        $hasOneFlag = false;
        $hasTwoFlag = false;
        $hasThreeFlag = false;
        /** @var EntityForCollection $entityForCollection */
        foreach ($entity->getCollection()->toArray() as $entityForCollection){
            if ($entityForCollection->getId() === 1) {
                $hasOneFlag = true;
            }

            if ($entityForCollection->getId() === 2) {
                $hasTwoFlag = true;
            }

            if ($entityForCollection->getId() === 3) {
                $hasThreeFlag = true;
            }
        }

        $this->assertTrue($hasOneFlag);
        $this->assertTrue($hasTwoFlag);
        $this->assertFalse($hasThreeFlag);
    }

    /**
     * @return void
     * @group my
     */
    public function testEditByEntity()
    {
        /** @var EntityWithCollection $response */
        $response = $this->service->create(CreateByArray::get());

        $collObj2 = $this->em->getRepository(EntityForCollection::class)->find(2);
        $collObj3 = $this->em->getRepository(EntityForCollection::class)->find(3);
        $response->addCollection($collObj2);
        $response->removeCollection($collObj3);

        /** @var EntityWithCollection $entity */
        $entity = $this->service->edit($response);

        $hasOneFlag = false;
        $hasTwoFlag = false;
        $hasThreeFlag = false;
        /** @var EntityForCollection $entityForCollection */
        foreach ($entity->getCollection()->toArray() as $entityForCollection){

            if ($entityForCollection->getId() === 1) {
                $hasOneFlag = true;
            }

            if ($entityForCollection->getId() === 2) {
                $hasTwoFlag = true;
            }

            if ($entityForCollection->getId() === 3) {
                $hasThreeFlag = true;
            }
        }

        $this->assertTrue($hasOneFlag);
        $this->assertTrue($hasTwoFlag);
        $this->assertFalse($hasThreeFlag);
    }
}