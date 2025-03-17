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
    // Create a user
    $managerUser = new User();
    $managerUser->setEmail('jane.smith@example.com');
    $managerUser->setPassword($this->passwordHasher->hashPassword($managerUser, 'password'));
    $managerUser->setEnabled(true);
    $managerUser->setCreatedAt(new \DateTimeImmutable());
    $managerUser->setRole('ROLE_MANAGER');
    $managerUser->setPerson($this->getReference('person_smith', Person::class));
    $manager->persist($managerUser);
    

    // Create a user for John Doe
    $user = new User();
    $user->setEmail('john.doe@example.com');
    $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
    $user->setEnabled(true);
    $user->setCreatedAt(new \DateTimeImmutable());
    $user->setRole('ROLE_COLLABORATOR');
    $user->setPerson($this->getReference( 'person_doe', Person::class));
    $manager->persist($user);

    // Create a user for Paul West
    $user1 = new User();
    $user1->setEmail('paul.west@example.com');
    $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password'));
    $user1->setEnabled(true);
    $user1->setCreatedAt(new \DateTimeImmutable());
    $user1->setRole('ROLE_COLLABORATOR');
    $user1->setPerson($this->getReference('person_west',Person::class));
    $manager->persist($user1);

    // Create a user for Bob Dylan
    $user2 = new User();
    $user2->setEmail('bob.dylan@example.com');
    $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password'));
    $user2->setEnabled(true);
    $user2->setCreatedAt(new \DateTimeImmutable());
    $user2->setRole('ROLE_MANAGER');
    $user2->setPerson($this->getReference('person_dylan',Person::class));
    $manager->persist($user2);

    // Create a user for Alice Johnson
    $user3 = new User();
    $user3->setEmail('alice.johnson@example.com');
    $user3->setPassword($this->passwordHasher->hashPassword($user3, 'password'));
    $user3->setEnabled(true);
    $user3->setCreatedAt(new \DateTimeImmutable());
    $user3->setRole('ROLE_COLLABORATOR');
    $user3->setPerson($this->getReference('person_johnson', Person::class));
    $manager->persist($user3);

    // Create a user for Eva Green
    $user4 = new User();
    $user4->setEmail('eva.green@example.com');
    $user4->setPassword($this->passwordHasher->hashPassword($user4, 'password'));
    $user4->setEnabled(true);
    $user4->setCreatedAt(new \DateTimeImmutable());
    $user4->setRole('ROLE_COLLABORATOR');
    $user4->setPerson($this->getReference('person_green',Person::class));
    $manager->persist($user4);

    // Create a user for Jack Black
    $user5 = new User();
    $user5->setEmail('jack.black@example.com');
    $user5->setPassword($this->passwordHasher->hashPassword($user5, 'password'));
    $user5->setEnabled(true);
    $user5->setCreatedAt(new \DateTimeImmutable());
    $user5->setRole('ROLE_MANAGER');
    $user5->setPerson($this->getReference('person_black',Person::class));
    $manager->persist($user5);

    // Create a user for Tom Phillips
    $user6 = new User();
    $user6->setEmail('tom.phillips@example.com');
    $user6->setPassword($this->passwordHasher->hashPassword($user6, 'password'));
    $user6->setEnabled(true);
    $user6->setCreatedAt(new \DateTimeImmutable());
    $user6->setRole('ROLE_COLLABORATOR');
    $user6->setPerson($this->getReference('person_phillips',Person::class));
    $manager->persist($user6);

    // Create a user for George Hanks
    $user7 = new User();
    $user7->setEmail('george.hanks@example.com');
    $user7->setPassword($this->passwordHasher->hashPassword($user7, 'password'));
    $user7->setEnabled(true);
    $user7->setCreatedAt(new \DateTimeImmutable());
    $user7->setRole('ROLE_MANAGER');
    $user7->setPerson($this->getReference('person_hanks',Person::class));
    $manager->persist($user7);

    // Create a user for Sam Harris
    $user8 = new User();
    $user8->setEmail('sam.harris@example.com');
    $user8->setPassword($this->passwordHasher->hashPassword($user8, 'password'));
    $user8->setEnabled(true);
    $user8->setCreatedAt(new \DateTimeImmutable());
    $user8->setRole('ROLE_COLLABORATOR');
    $user8->setPerson($this->getReference('person_harris',Person::class));
    $manager->persist($user8);

    // Create a user for Liam Cooper
    $user9 = new User();
    $user9->setEmail('liam.cooper@example.com');
    $user9->setPassword($this->passwordHasher->hashPassword($user9, 'password'));
    $user9->setEnabled(true);
    $user9->setCreatedAt(new \DateTimeImmutable());
    $user9->setRole('ROLE_COLLABORATOR');
    $user9->setPerson($this->getReference( 'person_cooper',Person::class));
    $manager->persist($user9);

    $manager->flush();
}

    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
        ];
    }
}
