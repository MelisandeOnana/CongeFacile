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
        $department = $manager->getRepository(Department::class)->findOneBy(['name' => 'IT']);
        $developerPosition = $manager->getRepository(Position::class)->findOneBy(['name' => 'Developer']);
        $managerPosition = $manager->getRepository(Position::class)->findOneBy(['name' => 'Manager']);
        $hrPosition = $manager->getRepository(Position::class)->findOneBy(['name' => 'HR']);
        $designerPosition = $manager->getRepository(Position::class)->findOneBy(['name' => 'Designer']);
        $anotherDepartment = $manager->getRepository(Department::class)->findOneBy(['name' => 'Finance']);

        // Create a manager 1 
        $managerPerson = new Person();
        $managerPerson->setFirstName('Jane');
        $managerPerson->setLastName('Smith');
        $managerPerson->setDepartment($department);
        $managerPerson->setPosition($managerPosition); // Assign the Manager position to Jane
        $managerPerson->setAlertOnAnswer(false);
        $managerPerson->setAlertNewRequest(false);
        $managerPerson->setAlertBeforeVacation(false);
        $manager->persist($managerPerson);

        // Create an employee and assign the manager
        $employee = new Person();
        $employee->setFirstName('John');
        $employee->setLastName('Doe');
        $employee->setDepartment($department);
        $employee->setPosition($developerPosition); // Assign the Developer position to John
        $employee->setManager($managerPerson); // Assign Jane as the manager
        $employee->setAlertOnAnswer(false);
        $employee->setAlertNewRequest(false);
        $employee->setAlertBeforeVacation(false);
        $manager->persist($employee);

        // Create another employee and assign the manager
        $employee2 = new Person();
        $employee2->setFirstName('Alice');
        $employee2->setLastName('Johnson');
        $employee2->setDepartment($department);
        $employee2->setPosition($developerPosition); // Assign the Developer position to Alice
        $employee2->setManager($managerPerson); // Assign Jane as the manager
        $employee2->setAlertOnAnswer(false);
        $employee2->setAlertNewRequest(false);
        $employee2->setAlertBeforeVacation(false);
        $manager->persist($employee2);

        // Create a HR employee
        $hrEmployee = new Person();
        $hrEmployee->setFirstName('Sarah');
        $hrEmployee->setLastName('Williams');
        $hrEmployee->setDepartment($department);
        $hrEmployee->setPosition($developerPosition); // Assign the HR position to Sarah
        $hrEmployee->setManager($managerPerson); // Assign Jane as the manager
        $hrEmployee->setAlertOnAnswer(false);
        $hrEmployee->setAlertNewRequest(false);
        $hrEmployee->setAlertBeforeVacation(false);
        $manager->persist($hrEmployee);

        // Create a designer
        $designer = new Person();
        $designer->setFirstName('Bob');
        $designer->setLastName('Martin');
        $designer->setDepartment($department);
        $designer->setPosition($developerPosition); // Assign the Designer position to Bob
        $designer->setManager($managerPerson); // Assign Jane as the manager
        $designer->setAlertOnAnswer(false);
        $designer->setAlertNewRequest(false);
        $designer->setAlertBeforeVacation(false);
        $manager->persist($designer);

        // Create a manager 2
        $managerPerson2 = new Person();
        $managerPerson2->setFirstName('Robert');
        $managerPerson2->setLastName('Johnson');
        $managerPerson2->setDepartment($anotherDepartment);
        $managerPerson2->setPosition($managerPosition); // Assign the Manager position to Robert
        $managerPerson2->setAlertOnAnswer(false);
        $managerPerson2->setAlertNewRequest(false);
        $managerPerson2->setAlertBeforeVacation(false);
        $manager->persist($managerPerson2);

        
       // Create an employee and assign the manager
        $employee3 = new Person();
        $employee3->setFirstName('Michael');
        $employee3->setLastName('Brown');
        $employee3->setDepartment($anotherDepartment);
        $employee3->setPosition($developerPosition); // Assign the Developer position to Michael
        $employee3->setManager($managerPerson2); // Assign Robert as the manager
        $employee3->setAlertOnAnswer(false);
        $employee3->setAlertNewRequest(false);
        $employee3->setAlertBeforeVacation(false);
        $manager->persist($employee3);

        // Create another employee and assign the manager
        $employee4 = new Person();
        $employee4->setFirstName('Emily');
        $employee4->setLastName('Clark');
        $employee4->setDepartment($anotherDepartment);
        $employee4->setPosition($developerPosition); // Assign the Developer position to Emily
        $employee4->setManager($managerPerson2); // Assign Robert as the manager
        $employee4->setAlertOnAnswer(false);
        $employee4->setAlertNewRequest(false);
        $employee4->setAlertBeforeVacation(false);
        $manager->persist($employee4);

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
