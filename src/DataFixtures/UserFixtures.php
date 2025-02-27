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
    if ($managerPerson) {
        $managerUser = new User();
        $managerUser->setEmail('jane.smith@example.com');
        $managerUser->setPassword($this->passwordHasher->hashPassword($managerUser, 'password'));
        $managerUser->setEnabled(true);
        $managerUser->setCreatedAt(new \DateTimeImmutable());
        $managerUser->setRole('ROLE_MANAGER');
        $managerUser->setPerson($managerPerson);
        $manager->persist($managerUser);
    }

    // Create a collaborator user 1
    $collaboratorPerson1 = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'John', 'lastName' => 'Doe']);
    if ($collaboratorPerson1) {
        $collaboratorUser1 = new User();
        $collaboratorUser1->setEmail('john.doe@example.com');
        $collaboratorUser1->setPassword($this->passwordHasher->hashPassword($collaboratorUser1, 'password'));
        $collaboratorUser1->setEnabled(true);
        $collaboratorUser1->setCreatedAt(new \DateTimeImmutable());
        $collaboratorUser1->setRole('ROLE_COLLABORATOR');
        $collaboratorUser1->setPerson($collaboratorPerson1);
        $manager->persist($collaboratorUser1);
    }

    // Create a collaborator user 2
    $collaboratorPerson2 = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'Alice', 'lastName' => 'Johnson']);
    if ($collaboratorPerson2) {
        $collaboratorUser2 = new User();
        $collaboratorUser2->setEmail('alice.johnson@example.com');
        $collaboratorUser2->setPassword($this->passwordHasher->hashPassword($collaboratorUser2, 'password'));
        $collaboratorUser2->setEnabled(true);
        $collaboratorUser2->setCreatedAt(new \DateTimeImmutable());
        $collaboratorUser2->setRole('ROLE_COLLABORATOR');
        $collaboratorUser2->setPerson($collaboratorPerson2);
        $manager->persist($collaboratorUser2);
    }

    // Create a collaborator user 3
    $collaboratorPerson3 = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'Bob', 'lastName' => 'Martin']);
    if ($collaboratorPerson3) {
        $collaboratorUser3 = new User();
        $collaboratorUser3->setEmail('bob.martin@example.com');
        $collaboratorUser3->setPassword($this->passwordHasher->hashPassword($collaboratorUser3, 'password'));
        $collaboratorUser3->setEnabled(true);
        $collaboratorUser3->setCreatedAt(new \DateTimeImmutable());
        $collaboratorUser3->setRole('ROLE_COLLABORATOR');
        $collaboratorUser3->setPerson($collaboratorPerson3);
        $manager->persist($collaboratorUser3);
    }

    // Create a collaborator user 4
    $collaboratorPerson4 = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'Sarah', 'lastName' => 'Williams']);
    if ($collaboratorPerson4) {
        $collaboratorUser4 = new User();
        $collaboratorUser4->setEmail('Sarah.williams@example.com');
        $collaboratorUser4->setPassword($this->passwordHasher->hashPassword($collaboratorUser4, 'password'));
        $collaboratorUser4->setEnabled(true);
        $collaboratorUser4->setCreatedAt(new \DateTimeImmutable());
        $collaboratorUser4->setRole('ROLE_COLLABORATOR');
        $collaboratorUser4->setPerson($collaboratorPerson4);
        $manager->persist($collaboratorUser4);
    }

    $manager->flush();
}

    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
        ];
    }
}
