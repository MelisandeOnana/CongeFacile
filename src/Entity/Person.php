<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Person $manager = null;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department = null;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Position $position = null;

    #[ORM\Column]
    private ?bool $alertOnAnswer = null;

    #[ORM\Column]
    private ?bool $alertNewRequest = null;

    #[ORM\Column]
    private ?bool $alertBeforeVacation = null;

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

    public function getManager(): ?Person
    {
        return $this->manager;
    }

    public function setManager(?Person $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
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