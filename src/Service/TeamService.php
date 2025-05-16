<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Person;
use App\Entity\Position;
use App\Repository\UserRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TeamService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private PersonRepository $personRepository
    ) {}

    public function isEmailUnique(string $email): bool
    {
        return !$this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    public function isManagerUnique($department, $position): bool
    {
        $existingManager = $this->personRepository->findOneBy([
            'department' => $department,
            'position' => $position,
        ]);
        return !$existingManager;
    }

    public function setDefaultPosition(Person $person, ?Position $position = null): void
    {
        if ($position) {
            $person->setPosition($position);
        } else {
            $defaultPosition = $this->entityManager->getRepository(Position::class)->find(1);
            $person->setPosition($defaultPosition);
        }
    }

    public function setUserRole(User $user, ?Position $position, $department): void
    {
        if ($position && $position->getName() === 'Manager' && $department) {
            $user->setRole('ROLE_MANAGER');
        } else {
            $user->setRole('ROLE_COLLABORATOR');
        }
    }

    public function setDefaultAlerts(Person $person): void
    {
        $person->setAlertOnAnswer(false);
        $person->setAlertNewRequest(false);
        $person->setAlertBeforeVacation(false);
    }

    public function setManager(Person $person, $department): void
    {
        if ($department) {
            $manager = $this->personRepository->findOneBy([
                'department' => $department,
                'manager' => null,
            ]);
            if ($manager) {
                $person->setManager($manager);
            }
        }
    }

    public function hashPassword(User $user, ?string $plainPassword): void
    {
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }
    }

    public function saveUserAndPerson(User $user, Person $person): void
    {
        $user->setPerson($person);
        $this->entityManager->persist($person);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function removeUserAndPerson(User $user, Person $person): void
    {
        $this->entityManager->remove($person);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}