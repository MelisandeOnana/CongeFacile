<?php

namespace App\Service;

use App\Entity\Position;
use App\Repository\PositionRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;

class PositionService
{
    public function __construct(
        private PositionRepository $positionRepository,
        private PersonRepository $personRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function getFilteredPositions($filterName)
    {
        $criteria = new \Doctrine\Common\Collections\Criteria();
        if ($filterName) {
            $criteria->andWhere($criteria->expr()->contains('name', $filterName));
        }
        $criteria->orderBy(['id' => 'DESC']);
        return $this->positionRepository->matching($criteria);
    }

    public function getPositionCounts($positions)
    {
        $counts = [];
        foreach ($positions as $position) {
            $counts[$position->getId()] = $this->personRepository->countByPosition($position);
        }
        return $counts;
    }

    public function isNameUnique(Position $position): bool
    {
        $existing = $this->positionRepository->findOneBy(['name' => $position->getName()]);
        return !$existing || $existing->getId() === $position->getId();
    }

    public function save(Position $position): bool
    {
        $this->entityManager->persist($position);
        try {
            $this->entityManager->flush();
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function canDelete(Position $position): bool
    {
        return $this->personRepository->countByPosition($position) === 0;
    }

    public function delete(Position $position): bool
    {
        $this->entityManager->remove($position);
        try {
            $this->entityManager->flush();
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}