<?php

namespace KirsanKifat\ApiServiceBundle\Tests\Mock\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use KirsanKifat\ApiServiceBundle\Tests\Mock\Repository\EntityWithCollectionRepository;

/**
 * @ORM\Entity(repositoryClass=EntityWithCollectionRepository::class)
 */
class EntityWithCollection
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;


    /**
     * @ORM\ManyToMany(targetEntity=EntityForCollection::class)
     */
    private $collection;


    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }


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

    /**
     * @return Collection<int, EntityForCollection>
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function addCollection(EntityForCollection $collection): self
    {
        if (!$this->collection->contains($collection)) {
            $this->collection[] = $collection;
        }

        return $this;
    }

    public function removeCollection(EntityForCollection $collection): self
    {
        $this->collection->removeElement($collection);

        return $this;
    }
}