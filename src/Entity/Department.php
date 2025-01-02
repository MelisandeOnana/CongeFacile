<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Collator;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    //relation entre depatment et person
    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'position')]
    private Collection $positions;

    //relation entre departement et request
    #[ORM\OneToMany(targetEntity: Request::class, mappedBy: 'department')]
    private Collection $departments;

    //fonctions de la relation
    public function getPositions(): ?Collection
    {
        return $this->positions;
    }

    public function getDepertments(): ?Collection
    {
        return $this->departments;
    }

    public function addDepartement(Department $department): void
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
        }
    }

    public function addPosition(Position $position): void
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
