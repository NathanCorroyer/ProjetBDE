<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $zipCode = null;

    #[ORM\OneToMany(targetEntity: Place::class, mappedBy: 'city', orphanRemoval: true)]
    private Collection $places;

    public function __construct()
    {
        $this->places = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlaces(Place $places): static
    {
        if (!$this->place->contains($places)) {
            $this->place->add($places);
            $places->setCity($this);
        }

        return $this;
    }

    public function removePlaces(Place $places): static
    {
        if ($this->places->removeElement($places)) {
            // set the owning side to null (unless already changed)
            if ($places->getCity() === $this) {
                $places->setCity(null);
            }
        }

        return $this;
    }
}
