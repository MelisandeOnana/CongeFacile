<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: RequestType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?RequestType $requestType = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $collaborator = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'text')]
    private ?string $comment = null;

    #[ORM\Column(type: 'text')]
    private ?string $answerComment = null;

    #[ORM\Column]
    private ?bool $answer = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $answerAt = null;

    #[ORM\Column]
    private ?string $receiptFile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestType(): ?RequestType
    {
        return $this->requestType;
    }

    public function setRequestType(?RequestType $requestType): static
    {
        $this->requestType = $requestType;

        return $this;
    }

    public function getCollaborator(): ?Person
    {
        return $this->collaborator;
    }

    public function setCollaborator(?Person $collaborator): static
    {
        $this->collaborator = $collaborator;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAnswerComment(): ?string
    {
        return $this->answerComment;
    }

    public function setAnswerComment(string $answerComment): static
    {
        $this->answerComment = $answerComment;

        return $this;
    }

    public function getAnswer(): ?bool
    {
        return $this->answer;
    }

    public function setAnswer(bool $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getAnswerAt(): ?\DateTimeImmutable
    {
        return $this->answerAt;
    }

    public function setAnswerAt(\DateTimeImmutable $answerAt): static
    {
        $this->answerAt = $answerAt;

        return $this;
    }

    public function getReceiptFile(): ?string
    {
        return $this->receiptFile;
    }

    public function setReceiptFile(string $receiptFile): static
    {
        $this->receiptFile = $receiptFile;

        return $this;
    }
}