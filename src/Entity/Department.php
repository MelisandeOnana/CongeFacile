<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity('name', message: 'Ce nom de département existe déjà.')]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    // @phpstan-ignore-next-line
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le nom du département est requis.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom du département ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: Person::class)]
    private Collection $collaborators;

    public function __construct()
    {
        $this->collaborators = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    public function addCollaborator(Person $collaborator): self
    {
        if (!$this->collaborators->contains($collaborator)) {
            $this->collaborators->add($collaborator);
            $collaborator->setDepartment($this);
        }

        return $this;
    }

    public function removeCollaborator(Person $collaborator): self
    {
        if ($this->collaborators->removeElement($collaborator)) {
            // Set the owning side to null (unless already changed)
            if ($collaborator->getDepartment() === $this) {
                $collaborator->setDepartment(null);
            }
        }

        return $this;
    }
}
