<?php

namespace App\Service;

use App\Entity\RequestType;
use App\Repository\RequestTypeRepository;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;

class RequestTypeService
{
    public function __construct(
        private RequestTypeRepository $requestTypeRepository,
        private RequestRepository $requestRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function getFilteredTypes(?string $filterName)
    {
        $criteria = Criteria::create();
        if ($filterName) {
            $criteria->andWhere($criteria->expr()->contains('name', $filterName));
        }
        $criteria->orderBy(['id' => 'DESC']);
        return $this->requestTypeRepository->matching($criteria);
    }

    public function getTypesCounts($types)
    {
        $counts = [];
        foreach ($types as $type) {
            $counts[$type->getId()] = $this->requestRepository->countRequestsByRequestType($type);
        }
        return $counts;
    }

    public function isNameUnique(RequestType $requestType): bool
    {
        $existing = $this->requestTypeRepository->findOneBy(['name' => $requestType->getName()]);
        return !$existing || $existing->getId() === $requestType->getId();
    }

    public function save(RequestType $requestType): void
    {
        $this->entityManager->persist($requestType);
        $this->entityManager->flush();
    }

    public function canDelete(RequestType $requestType): bool
    {
        return $this->requestRepository->countRequestsByRequestType($requestType) === 0;
    }

    public function delete(RequestType $requestType): void
    {
        $this->entityManager->remove($requestType);
        $this->entityManager->flush();
    }
}