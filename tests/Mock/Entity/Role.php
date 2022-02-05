<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Repository\RoleRepository;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @ExclusionPolicy("none")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $name;

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
