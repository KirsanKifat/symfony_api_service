<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Entity;

use Doctrine\ORM\Mapping as ORM;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Repository\RoleWithOnlyDoctrineAnnotationRepository;

/**
 * @ORM\Entity(repositoryClass=RoleWithOnlyDoctrineAnnotationRepository::class)
 */
class RoleWIthOnlyDoctrineAnnotation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}