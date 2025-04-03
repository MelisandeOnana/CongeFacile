<?php

namespace App\Entity;

use App\Enum\Statut;
use App\Repository\RequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: RequestType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Le type de demande ne peut pas être vide.')]
    private ?RequestType $requestType = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Le collaborateur ne peut pas être vide.')]
    private ?Person $collaborator = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de début ne peut pas être vide.')]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de fin ne peut pas être vide.')]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $answerComment = null;

    #[ORM\Column]
    private ?Statut $answer = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $answerAt = null;

    #[ORM\Column(nullable: true)]
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

    public function setStartAt(?\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAnswerComment(): ?string
    {
        return $this->answerComment;
    }

    public function setAnswerComment(?string $answerComment): static
    {
        $this->answerComment = $answerComment;

        return $this;
    }

    public function getAnswer(): ?Statut
    {
        return $this->answer;
    }

    public function setAnswer(?int $answer): static
    {
        if (! Statut::tryFrom($answer)) {
            throw new \InvalidArgumentException('Valeur de statut invalide.');
        }

        $this->answer = Statut::from($answer);

        return $this;
    }

    public function getAnswerAt(): ?\DateTimeImmutable
    {
        return $this->answerAt;
    }

    public function setAnswerAt(?\DateTimeImmutable $answerAt): static
    {
        $this->answerAt = $answerAt;

        return $this;
    }

    public function getReceiptFile(): ?string
    {
        return $this->receiptFile;
    }

    public function setReceiptFile(?string $receiptFile): static
    {
        $this->receiptFile = $receiptFile;

        return $this;
    }

    public function getWorkingDays(): float
    {
        $start = $this->getStartAt();
        $end = $this->getEndAt();

        if (! $start || ! $end) {
            return 0;
        }

        // Copier les dates pour éviter de modifier l'original avec les horaires
        $tempStart = clone $start;
        $tempEnd = clone $end;

        // Réinitialiser l'heure à 00:00:00 pour le calcul des jours
        $tempStart->setTime(0, 0, 0);
        $tempEnd->setTime(0, 0, 0);

        $interval = $tempStart->diff($tempEnd);
        $days = $interval->days + 1;

        // Calculer les jours ouvrés en excluant les samedis et dimanches
        $workingDays = 0;
        for ($i = 0; $i < $days; ++$i) {
            $currentDay = (clone $tempStart)->modify("+$i days");
            $dayOfWeek = $currentDay->format('N'); // 1 (lundi) à 7 (dimanche)

            if ($dayOfWeek < 6) { // Exclure samedi (6) et dimanche (7)
                ++$workingDays;
            }
        }

        // Calculer les demi-journées
        $halfDays = 0;
        if ($start->format('H:i') >= '12:00' && $start->format('N') < 6) {
            $halfDays += 0.5;
        }
        if ($end->format('H:i') <= '12:00' && $end->format('N') < 6) {
            $halfDays += 0.5;
        }

        return $workingDays - $halfDays;
    }
}
