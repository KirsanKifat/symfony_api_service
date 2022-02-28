<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Entity;

use Doctrine\ORM\Mapping as ORM;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Repository\UserWithOnlyDoctrineAnnotationRepository;

/**
 * @ORM\Entity(repositoryClass=UserWithOnlyDoctrineAnnotationRepository::class)
 */
class UserWithOnlyDoctrineAnnotation
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
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=RoleWIthOnlyDoctrineAnnotation::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?RoleWIthOnlyDoctrineAnnotation
    {
        return $this->role;
    }

    public function setRole(?RoleWIthOnlyDoctrineAnnotation $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}