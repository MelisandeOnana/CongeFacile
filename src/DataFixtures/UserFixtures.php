<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create a manager user
        $managerPerson = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'Jane', 'lastName' => 'Smith']);

        $managerUser = new User();
        $managerUser->setEmail('jane.smith@example.com');
        $managerUser->setPassword($this->passwordHasher->hashPassword($managerUser, 'password'));
        $managerUser->setEnabled(true);
        $managerUser->setCreatedAt(new \DateTimeImmutable());
        $managerUser->setRole('ROLE_MANAGER');
        $managerUser->setPerson($managerPerson);
        $manager->persist($managerUser);

        // Create a collaborator user
        $collaboratorPerson = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'John', 'lastName' => 'Doe']);

        $collaboratorUser = new User();
        $collaboratorUser->setEmail('john.doe@example.com');
        $collaboratorUser->setPassword($this->passwordHasher->hashPassword($collaboratorUser, 'password'));
        $collaboratorUser->setEnabled(true);
        $collaboratorUser->setCreatedAt(new \DateTimeImmutable());
        $collaboratorUser->setRole('ROLE_COLLABORATOR');
        $collaboratorUser->setPerson($collaboratorPerson);
        $manager->persist($collaboratorUser);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
        ];
    }
}