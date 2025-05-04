<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // @phpstan-ignore-next-line
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de famille ne peut pas être vide.')]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide.')]
    private ?string $firstName = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Person $manager = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'collaborators')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Le département ne peut pas être vide.')]
    private ?Department $department = null;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Le poste ne peut pas être vide.')]
    private ?Position $position = null;

    #[ORM\Column]
    private bool $alertOnAnswer = false;

    #[ORM\Column]
    private bool $alertNewRequest = false;

    #[ORM\Column]
    private bool $alertBeforeVacation = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getManager(): ?self
    {
        return $this->manager;
    }

    public function setManager(?self $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getAlertOnAnswer(): ?bool
    {
        return $this->alertOnAnswer;
    }

    public function setAlertOnAnswer(bool $alertOnAnswer): static
    {
        $this->alertOnAnswer = $alertOnAnswer;

        return $this;
    }

    public function getAlertNewRequest(): ?bool
    {
        return $this->alertNewRequest;
    }

    public function setAlertNewRequest(bool $alertNewRequest): static
    {
        $this->alertNewRequest = $alertNewRequest;

        return $this;
    }

    public function getAlertBeforeVacation(): ?bool
    {
        return $this->alertBeforeVacation;
    }

    public function setAlertBeforeVacation(bool $alertBeforeVacation): static
    {
        $this->alertBeforeVacation = $alertBeforeVacation;

        return $this;
    }
}
