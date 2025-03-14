<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\Department;
use App\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PersonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer les départements et les postes existants
        $managerPosition = $manager->getRepository(Position::class)->findOneBy(['name' => 'Manager']);
    

        // Create a manager 1 
        $managerPerson = new Person();
        $managerPerson->setFirstName('Jane');
        $managerPerson->setLastName('Smith');
        $managerPerson->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'Symfony']));
        $managerPerson->setPosition($managerPosition); // Assign the Manager position to Jane
        $managerPerson->setAlertOnAnswer(false);
        $managerPerson->setAlertNewRequest(false);
        $managerPerson->setAlertBeforeVacation(false);
        $manager->persist($managerPerson);

        // Create an employee and assign the manager
        $employee = new Person();
        $employee->setFirstName('John');
        $employee->setLastName('Doe');
        $employee->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'Symfony']));
        $employee->setPosition($manager->getRepository(Position::class)->findOneBy(['name' => 'Developpeur'])); 
        $employee->setManager($managerPerson); // Assign Jane as the manager
        $employee->setAlertOnAnswer(false);
        $employee->setAlertNewRequest(false);
        $employee->setAlertBeforeVacation(false);
        $manager->persist($employee);

        $employee1 = new Person();
        $employee1->setFirstName('Paul');
        $employee1->setLastName('West');
        $employee1->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'Symfony']));
        $employee1->setPosition($manager->getRepository(Position::class)->findOneBy(['name' => 'DevOps']));
        $employee1->setManager($managerPerson);
        $employee1->setAlertOnAnswer(false);
        $employee1->setAlertNewRequest(false);
        $employee1->setAlertBeforeVacation(false);
        $manager->persist($employee1);

        // Create a manager 2
        $managerPerson2 = new Person();
        $managerPerson2->setFirstName('Bob');
        $managerPerson2->setLastName('Dylan');
        $managerPerson2->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'Pôle UX']));
        $managerPerson2->setPosition($managerPosition); // Assign the Manager position to Bob
        $managerPerson2->setAlertOnAnswer(false);
        $managerPerson2->setAlertNewRequest(false);
        $managerPerson2->setAlertBeforeVacation(false);
        $manager->persist($managerPerson2);

        
        $employee2 = new Person();
        $employee2->setFirstName('Alice');
        $employee2->setLastName('Johnson');
        $employee2->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'Pôle UX']));
        $employee2->setPosition($manager->getRepository(Position::class)->findOneBy(['name' => 'Designeur'])); 
        $employee2->setManager($managerPerson2); 
        $employee2->setAlertOnAnswer(false);
        $employee2->setAlertNewRequest(false);
        $employee2->setAlertBeforeVacation(false);
        $manager->persist($employee2);

        $employee3 = new Person();
        $employee3->setFirstName('Eva');
        $employee3->setLastName('Green');
        $employee3->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'Pôle UX']));
        $employee3->setPosition($manager->getRepository(Position::class)->findOneBy(['name' => 'Designeur']));
        $employee3->setManager($managerPerson2); 
        $employee3->setAlertOnAnswer(false);
        $employee3->setAlertNewRequest(false);
        $employee3->setAlertBeforeVacation(false);
        $manager->persist($employee3);

        $managerPerson3 = new Person();
        $managerPerson3->setFirstName('Jack');
        $managerPerson3->setLastName('Black');
        $managerPerson3->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'Marketing']));
        $managerPerson3->setPosition($managerPosition); // Assign the Manager position to Jack
        $managerPerson3->setAlertOnAnswer(false);
        $managerPerson3->setAlertNewRequest(false);
        $managerPerson3->setAlertBeforeVacation(false);
        $manager->persist($managerPerson3);

        $employee4 = new Person();
        $employee4->setFirstName('Tom');
        $employee4->setLastName('Phillips');
        $employee4->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'Marketing']));
        $employee4->setPosition($manager->getRepository(Position::class)->findOneBy(['name' => 'Commercial']));
        $employee4->setManager($managerPerson3);
        $employee4->setAlertOnAnswer(false);
        $employee4->setAlertNewRequest(false);
        $employee4->setAlertBeforeVacation(false);
        $manager->persist($employee4);

        $managerPerson4 = new Person();
        $managerPerson4->setFirstName('George');
        $managerPerson4->setLastName('Hanks');
        $managerPerson4->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'CMS']));
        $managerPerson4->setPosition($managerPosition); // Assign the Manager position to George
        $managerPerson4->setAlertOnAnswer(false);
        $managerPerson4->setAlertNewRequest(false);
        $managerPerson4->setAlertBeforeVacation(false);
        $manager->persist($managerPerson4);

        $employee5 = new Person();
        $employee5->setFirstName('Sam');
        $employee5->setLastName('Harris');
        $employee5->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'CMS']));
        $employee5->setPosition($manager->getRepository(Position::class)->findOneBy(['name' => 'Developpeur']));
        $employee5->setManager($managerPerson4);
        $employee5->setAlertOnAnswer(false);
        $employee5->setAlertNewRequest(false);
        $employee5->setAlertBeforeVacation(false);
        $manager->persist($employee5);

        $employee6 = new Person();
        $employee6->setFirstName('Liam');
        $employee6->setLastName('Cooper');
        $employee6->setDepartment($manager->getRepository(Department::class)->findOneBy(['name' => 'CMS']));
        $employee6->setPosition($manager->getRepository(Position::class)->findOneBy(['name' => 'Developpeur']));
        $employee6->setManager($managerPerson4);
        $employee6->setAlertOnAnswer(false);
        $employee6->setAlertNewRequest(false);
        $employee6->setAlertBeforeVacation(false);
        $manager->persist($employee6);

        // Flush all the entities to the database
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DepartmentFixtures::class,
            PositionFixtures::class,
        ];
    }
}
