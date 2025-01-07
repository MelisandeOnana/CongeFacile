<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $manager = null;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    private ?Department $department = null;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    private ?Position $position = null;

    #[ORM\Column(type: 'boolean')]
    private bool $alertNewRequest;

    #[ORM\Column(type: 'boolean')]
    private bool $alertOnAnswer;

    #[ORM\Column(type: 'boolean')]
    private bool $alertBeforeVacation;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getManager(): ?self
    {
        return $this->manager;
    }

    public function setManager(?self $manager): self
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

    public function setPosition(?Position $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function isAlertNewRequest(): bool
    {
        return $this->alertNewRequest;
    }

    public function setAlertNewRequest(bool $alertNewRequest): self
    {
        $this->alertNewRequest = $alertNewRequest;
        return $this;
    }

    public function isAlertOnAnswer(): bool
    {
        return $this->alertOnAnswer;
    }

    public function setAlertOnAnswer(bool $alertOnAnswer): self
    {
        $this->alertOnAnswer = $alertOnAnswer;
        return $this;
    }

    public function isAlertBeforeVacation(): bool
    {
        return $this->alertBeforeVacation;
    }

    public function setAlertBeforeVacation(bool $alertBeforeVacation): self
    {
        $this->alertBeforeVacation = $alertBeforeVacation;
        return $this;
    }
}
