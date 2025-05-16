<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Person;
use App\Repository\PersonRepository;
use App\Repository\PositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ManagerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PersonRepository $personRepository,
        private PositionRepository $positionRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function isDepartmentAvailable($department, $excludePersonId = null): bool
    {
        $existingManager = $this->personRepository->findOneBy(['department' => $department]);
        if ($existingManager && (!$excludePersonId || $existingManager->getId() !== $excludePersonId)) {
            return false;
        }
        return true;
    }

    public function assignManagerPosition(User $manager): void
    {
        $managerPosition = $this->positionRepository->findOneBy(['name' => 'Manager']);
        if ($managerPosition) {
            $manager->setPosition($managerPosition);
        }
    }

    public function assignDepartment(User $manager, $department): bool
    {
        if (!$department) {
            return false;
        }
        $manager->getPerson()->setDepartment($department);
        return true;
    }

    public function hashPassword(User $manager, ?string $plainPassword): void
    {
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($manager, $plainPassword);
            $manager->setPassword($hashedPassword);
        }
    }

    public function saveManager(User $manager): void
    {
        $this->entityManager->persist($manager->getPerson());
        $this->entityManager->persist($manager);
        $this->entityManager->flush();
    }
}