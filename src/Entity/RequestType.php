<?php

namespace App\Entity;

use App\Repository\RequestTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: RequestTypeRepository::class)]
class RequestType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    //relation entre request_type et request
    #[ORM\OneToMany(targetEntity: Request::class, mappedBy: 'request_type')]
    private Collection $requests_type;

    //fonctions de la relation
    public function getRequestsTypes(): ?Collection
    {
        return $this->requests_type;
    }

    public function addRequestType(RequestType $request_type): void
    {
        if (!$this->requests_type->contains($request_type)) {
            $this->requests_type->add($request_type);
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
