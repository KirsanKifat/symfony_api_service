<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Entity;

use Doctrine\ORM\Mapping as ORM;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Repository\RecursiveObjectTwoRepository;

/**
 * @ORM\Entity(repositoryClass=RecursiveObjectTwoRepository::class)
 */
class RecursiveObjectTwo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=RecursiveObjectOne::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private RecursiveObjectOne $subObject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubObject(): ?RecursiveObjectOne
    {
        return $this->subObject;
    }

    public function setSubObject(RecursiveObjectOne $subObject): self
    {
        $this->subObject = $subObject;

        return $this;
    }
}