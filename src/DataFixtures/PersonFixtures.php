<?php

namespace App\DataFixtures;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PersonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Create a manager 1
        $managerPerson = new Person();
        $managerPerson->setFirstName('Jane');
        $managerPerson->setLastName('Smith');
        $managerPerson->setDepartment($this->getReference('department_symfony', Department::class));
        $managerPerson->setPosition($this->getReference('position_manager', Position::class));
        $managerPerson->setAlertOnAnswer(false);
        $managerPerson->setAlertNewRequest(true);
        $managerPerson->setAlertBeforeVacation(false);
        $manager->persist($managerPerson);
        $this->addReference('person_smith', $managerPerson);

        // Create an employee and assign the manager
        $employee = new Person();
        $employee->setFirstName('John');
        $employee->setLastName('Doe');
        $employee->setDepartment($this->getReference('department_symfony', Department::class));
        $employee->setPosition($this->getReference('position_developpeur', Position::class));
        $employee->setManager($managerPerson);
        $employee->setAlertOnAnswer(true);
        $employee->setAlertNewRequest(false);
        $employee->setAlertBeforeVacation(false);
        $manager->persist($employee);
        $this->addReference('person_doe', $employee);

        $employee1 = new Person();
        $employee1->setFirstName('Paul');
        $employee1->setLastName('West');
        $employee1->setDepartment($this->getReference('department_symfony', Department::class));
        $employee1->setPosition($this->getReference('position_devops', Position::class));
        $employee1->setManager($managerPerson);
        $employee1->setAlertOnAnswer(false);
        $employee1->setAlertNewRequest(false);
        $employee1->setAlertBeforeVacation(true);
        $manager->persist($employee1);
        $this->addReference('person_west', $employee1);

        // Create a manager 2
        $managerPerson2 = new Person();
        $managerPerson2->setFirstName('Bob');
        $managerPerson2->setLastName('Dylan');
        $managerPerson2->setDepartment($this->getReference('department_pôle_ux', Department::class));
        $managerPerson2->setPosition($this->getReference('position_manager', Position::class));
        $managerPerson2->setAlertOnAnswer(false);
        $managerPerson2->setAlertNewRequest(false);
        $managerPerson2->setAlertBeforeVacation(false);
        $manager->persist($managerPerson2);
        $this->addReference('person_dylan', $managerPerson2);

        $employee2 = new Person();
        $employee2->setFirstName('Alice');
        $employee2->setLastName('Johnson');
        $employee2->setDepartment($this->getReference('department_pôle_ux', Department::class));
        $employee2->setPosition($this->getReference('position_designeur', Position::class));
        $employee2->setManager($managerPerson2);
        $employee2->setAlertOnAnswer(true);
        $employee2->setAlertNewRequest(false);
        $employee2->setAlertBeforeVacation(true);
        $manager->persist($employee2);
        $this->addReference('person_johnson', $employee2);

        $employee3 = new Person();
        $employee3->setFirstName('Eva');
        $employee3->setLastName('Green');
        $employee3->setDepartment($this->getReference('department_pôle_ux', Department::class));
        $employee3->setPosition($this->getReference('position_designeur', Position::class));
        $employee3->setManager($managerPerson2);
        $employee3->setAlertOnAnswer(false);
        $employee3->setAlertNewRequest(false);
        $employee3->setAlertBeforeVacation(false);
        $manager->persist($employee3);
        $this->addReference('person_green', $employee3);

        $managerPerson3 = new Person();
        $managerPerson3->setFirstName('Jack');
        $managerPerson3->setLastName('Black');
        $managerPerson3->setDepartment($this->getReference('department_marketing', Department::class));
        $managerPerson3->setPosition($this->getReference('position_manager', Position::class));
        $managerPerson3->setAlertOnAnswer(false);
        $managerPerson3->setAlertNewRequest(false);
        $managerPerson3->setAlertBeforeVacation(false);
        $manager->persist($managerPerson3);
        $this->addReference('person_black', $managerPerson3);

        $employee4 = new Person();
        $employee4->setFirstName('Tom');
        $employee4->setLastName('Phillips');
        $employee4->setDepartment($this->getReference('department_marketing', Department::class));
        $employee4->setPosition($this->getReference('position_commercial', Position::class));
        $employee4->setManager($managerPerson3);
        $employee4->setAlertOnAnswer(false);
        $employee4->setAlertNewRequest(false);
        $employee4->setAlertBeforeVacation(false);
        $manager->persist($employee4);
        $this->addReference('person_phillips', $employee4);

        $managerPerson4 = new Person();
        $managerPerson4->setFirstName('George');
        $managerPerson4->setLastName('Hanks');
        $managerPerson4->setDepartment($this->getReference('department_cms', Department::class));
        $managerPerson4->setPosition($this->getReference('position_manager', Position::class));
        $managerPerson4->setAlertOnAnswer(false);
        $managerPerson4->setAlertNewRequest(false);
        $managerPerson4->setAlertBeforeVacation(false);
        $manager->persist($managerPerson4);
        $this->addReference('person_hanks', $managerPerson4);

        $employee5 = new Person();
        $employee5->setFirstName('Sam');
        $employee5->setLastName('Harris');
        $employee5->setDepartment($this->getReference('department_cms', Department::class));
        $employee5->setPosition($this->getReference('position_developpeur', Position::class));
        $employee5->setManager($managerPerson4);
        $employee5->setAlertOnAnswer(false);
        $employee5->setAlertNewRequest(false);
        $employee5->setAlertBeforeVacation(false);
        $manager->persist($employee5);
        $this->addReference('person_harris', $employee5);

        $employee6 = new Person();
        $employee6->setFirstName('Liam');
        $employee6->setLastName('Cooper');
        $employee6->setDepartment($this->getReference('department_cms', Department::class));
        $employee6->setPosition($this->getReference('position_developpeur', Position::class));
        $employee6->setManager($managerPerson4);
        $employee6->setAlertOnAnswer(false);
        $employee6->setAlertNewRequest(false);
        $employee6->setAlertBeforeVacation(false);
        $manager->persist($employee6);
        $this->addReference('person_cooper', $employee6);

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
