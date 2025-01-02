<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
class Position
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    //relation entre position et person
    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'position')]
    private Collection $positions;

    //fonctions de la relation
    public function getPositions(): ?Collection
    {
        return $this->positions;
    }

    public function addRequestType(Position $position): void
    {
        if (!$this->positions->contains($position)) {
            $this->positions->add($position);
        }
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
}
