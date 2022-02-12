<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Entity;

use Doctrine\ORM\Mapping as ORM;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Repository\RecursiveObjectOneRepository;

/**
 * @ORM\Entity(repositoryClass=RecursiveObjectOneRepository::class)
 */
class RecursiveObjectOne
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=RecursiveObjectTwo::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private RecursiveObjectTwo $subObject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubObject(): ?RecursiveObjectTwo
    {
        return $this->subObject;
    }

    public function setSubObject(RecursiveObjectTwo $subObject): self
    {
        $this->subObject = $subObject;

        return $this;
    }
}