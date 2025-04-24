<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private const ROLE_MANAGER = 'ROLE_MANAGER';
    private const ROLE_COLLABORATOR = 'ROLE_COLLABORATOR';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'jane.smith@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_MANAGER,
                'person' => 'person_smith',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'john.doe@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_COLLABORATOR,
                'person' => 'person_doe',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'paul.west@example.com',
                'password' => 'password',
                'enabled' => false,
                'role' => self::ROLE_COLLABORATOR,
                'person' => 'person_west',
                'createdAt' => new \DateTimeImmutable('2024-12-01 15:02:10'),
            ],
            [
                'email' => 'bob.dylan@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_MANAGER,
                'person' => 'person_dylan',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'alice.johnson@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_COLLABORATOR,
                'person' => 'person_johnson',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'eva.green@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_COLLABORATOR,
                'person' => 'person_green',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'jack.black@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_MANAGER,
                'person' => 'person_black',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'tom.phillips@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_COLLABORATOR,
                'person' => 'person_phillips',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'george.hanks@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_MANAGER,
                'person' => 'person_hanks',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'sam.harris@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_COLLABORATOR,
                'person' => 'person_harris',
                'createdAt' => new \DateTimeImmutable(),
            ],
            [
                'email' => 'liam.cooper@example.com',
                'password' => 'password',
                'enabled' => true,
                'role' => self::ROLE_COLLABORATOR,
                'person' => 'person_cooper',
                'createdAt' => new \DateTimeImmutable(),
            ],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setEnabled($userData['enabled']);
            $user->setCreatedAt($userData['createdAt']);
            $user->setRole($userData['role']);
            $user->setPerson($this->getReference($userData['person'], Person::class));
            $manager->persist($user);
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
