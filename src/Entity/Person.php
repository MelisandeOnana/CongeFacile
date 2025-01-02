<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(nullable:true)]
    private ?int $manager_id = null;

    #[ORM\Column]
    private ?int $department_id = null;

    #[ORM\Column]
    private ?int $position_id = null;

    #[ORM\Column]
    private ?bool $alert_new_request = null;

    #[ORM\Column]
    private ?bool $alert_on_answer = null;

    #[ORM\Column]
    private ?bool $alert_before_vacation = null;

    //relation entre person et department
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'departments')]
    private Department $department;

    // relation entre person et position
    #[ORM\ManyToOne(targetEntity: Position::class, inversedBy: 'positions')]
    private Position $position;

    //relation entre person et manager
    #[ORM\ManyToOne(targetEntity: user::class, inversedBy: 'managers')]
    private user $manager;

    //relation entre person et request
    #[ORM\ManyToMany(targetEntity: Request::class, inversedBy: 'requests')]
    private Collection $requests;

    //fonctions de la relation request
    public function getRequests(): ?Collection
    {
        return $this->requests;
    }

    public function addRequests(Request $request): void
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
        }
    }

    //fonctions de la relation department
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): void
    {
        $this->department =$department;
    }

    //fonctions de la relation position
    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): void
    {
        $this->position =$position;
    }

    //fonctions de la relation manager
    public function getManager(): ?user
    {
        return $this->manager;
    }

    public function setManager(user $manager): void
    {
        $this->manager =$manager;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getManagerId(): ?int
    {
        return $this->manager_id;
    }

    public function setManagerId(int $manager_id): static
    {
        $this->manager_id = $manager_id;

        return $this;
    }

    public function getDepartmentId(): ?int
    {
        return $this->department_id;
    }

    public function setDepartmentId(int $department_id): static
    {
        $this->department_id = $department_id;

        return $this;
    }

    public function getPositionId(): ?int
    {
        return $this->position_id;
    }

    public function setPositionId(int $position_id): static
    {
        $this->position_id = $position_id;

        return $this;
    }

    public function isAlertNewRequest(): ?bool
    {
        return $this->alert_new_request;
    }

    public function setAlertNewRequest(bool $alert_new_request): static
    {
        $this->alert_new_request = $alert_new_request;

        return $this;
    }

    public function isAlertOnAnswer(): ?bool
    {
        return $this->alert_on_answer;
    }

    public function setAlertOnAnswer(bool $alert_on_answer): static
    {
        $this->alert_on_answer = $alert_on_answer;

        return $this;
    }

    public function isAlertBeforeVacation(): ?bool
    {
        return $this->alert_before_vacation;
    }

    public function setAlertBeforeVacation(bool $alert_before_vacation): static
    {
        $this->alert_before_vacation = $alert_before_vacation;

        return $this;
    }
}
