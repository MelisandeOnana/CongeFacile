<?php

namespace App\DataFixtures;

use App\Entity\Department;
use App\Entity\Person;
use App\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PersonFixtures extends Fixture implements DependentFixtureInterface
{
    private const DEPARTMENT_SYMFONY = 'Symfony';
    private const DEPARTMENT_POLE_UX = 'Pôle_ux';
    private const DEPARTMENT_MARKETING = 'Marketing';
    private const DEPARTMENT_CMS = 'CMS';

    private const POSITION_MANAGER = 'position_manager';
    private const POSITION_DEVELOPPEUR = 'position_developpeur'; // Supprimer l'accent
    private const POSITION_DESIGNEUR = 'position_designeur';
    private const POSITION_COMMERCIAL = 'position_commercial';
    private const POSITION_DEVOPS = 'position_devops';
    public function load(ObjectManager $manager): void
    {
        // Création d'un manager 1
        $managerPerson = new Person();
        $managerPerson->setFirstName('Jane');
        $managerPerson->setLastName('Smith');
        $managerPerson->setDepartment($this->getReference(self::DEPARTMENT_SYMFONY, Department::class));
        $managerPerson->setPosition($this->getReference(self::POSITION_MANAGER, Position::class));
        $managerPerson->setAlertOnAnswer(false);
        $managerPerson->setAlertNewRequest(true);
        $managerPerson->setAlertBeforeVacation(false);
        $manager->persist($managerPerson);
        $this->addReference('person_smith', $managerPerson);

        // Création d'un employé et assignation au manager
        $employee = new Person();
        $employee->setFirstName('John');
        $employee->setLastName('Doe');
        $employee->setDepartment($this->getReference(self::DEPARTMENT_SYMFONY, Department::class));
        $employee->setPosition($this->getReference(self::POSITION_DEVELOPPEUR, Position::class));
        $employee->setManager($managerPerson);
        $employee->setAlertOnAnswer(true);
        $employee->setAlertNewRequest(false);
        $employee->setAlertBeforeVacation(false);
        $manager->persist($employee);
        $this->addReference('person_doe', $employee);

        // Ajout d'autres employés et managers
        $employee1 = new Person();
        $employee1->setFirstName('Paul');
        $employee1->setLastName('West');
        $employee1->setDepartment($this->getReference(self::DEPARTMENT_SYMFONY, Department::class));
        $employee1->setPosition($this->getReference(self::POSITION_DEVOPS, Position::class));
        $employee1->setManager($managerPerson);
        $employee1->setAlertOnAnswer(false);
        $employee1->setAlertNewRequest(false);
        $employee1->setAlertBeforeVacation(true);
        $manager->persist($employee1);
        $this->addReference('person_west', $employee1);

        // Création d'un manager 2
        $managerPerson2 = new Person();
        $managerPerson2->setFirstName('Bob');
        $managerPerson2->setLastName('Dylan');
        $managerPerson2->setDepartment($this->getReference(self::DEPARTMENT_POLE_UX, Department::class));
        $managerPerson2->setPosition($this->getReference(self::POSITION_MANAGER, Position::class));
        $managerPerson2->setAlertOnAnswer(false);
        $managerPerson2->setAlertNewRequest(false);
        $managerPerson2->setAlertBeforeVacation(false);
        $manager->persist($managerPerson2);
        $this->addReference('person_dylan', $managerPerson2);

        // Ajout d'autres employés
        $employee2 = new Person();
        $employee2->setFirstName('Alice');
        $employee2->setLastName('Johnson');
        $employee2->setDepartment($this->getReference(self::DEPARTMENT_POLE_UX, Department::class));
        $employee2->setPosition($this->getReference(self::POSITION_DESIGNEUR, Position::class));
        $employee2->setManager($managerPerson2);
        $employee2->setAlertOnAnswer(true);
        $employee2->setAlertNewRequest(false);
        $employee2->setAlertBeforeVacation(true);
        $manager->persist($employee2);
        $this->addReference('person_johnson', $employee2);

        $employee3 = new Person();
        $employee3->setFirstName('Eva');
        $employee3->setLastName('Green');
        $employee3->setDepartment($this->getReference(self::DEPARTMENT_POLE_UX, Department::class));
        $employee3->setPosition($this->getReference(self::POSITION_DESIGNEUR, Position::class));
        $employee3->setManager($managerPerson2);
        $employee3->setAlertOnAnswer(false);
        $employee3->setAlertNewRequest(false);
        $employee3->setAlertBeforeVacation(false);
        $manager->persist($employee3);
        $this->addReference('person_green', $employee3);

        $managerPerson3 = new Person();
        $managerPerson3->setFirstName('Jack');
        $managerPerson3->setLastName('Black');
        $managerPerson3->setDepartment($this->getReference(self::DEPARTMENT_MARKETING, Department::class));
        $managerPerson3->setPosition($this->getReference(self::POSITION_MANAGER, Position::class));
        $managerPerson3->setAlertOnAnswer(false);
        $managerPerson3->setAlertNewRequest(false);
        $managerPerson3->setAlertBeforeVacation(false);
        $manager->persist($managerPerson3);
        $this->addReference('person_black', $managerPerson3);

        $employee4 = new Person();
        $employee4->setFirstName('Tom');
        $employee4->setLastName('Phillips');
        $employee4->setDepartment($this->getReference(self::DEPARTMENT_MARKETING, Department::class));
        $employee4->setPosition($this->getReference(self::POSITION_COMMERCIAL, Position::class));
        $employee4->setManager($managerPerson3);
        $employee4->setAlertOnAnswer(false);
        $employee4->setAlertNewRequest(false);
        $employee4->setAlertBeforeVacation(false);
        $manager->persist($employee4);
        $this->addReference('person_phillips', $employee4);

        $managerPerson4 = new Person();
        $managerPerson4->setFirstName('George');
        $managerPerson4->setLastName('Hanks');
        $managerPerson4->setDepartment($this->getReference(self::DEPARTMENT_CMS, Department::class));
        $managerPerson4->setPosition($this->getReference(self::POSITION_MANAGER, Position::class));
        $managerPerson4->setAlertOnAnswer(false);
        $managerPerson4->setAlertNewRequest(false);
        $managerPerson4->setAlertBeforeVacation(false);
        $manager->persist($managerPerson4);
        $this->addReference('person_hanks', $managerPerson4);

        $employee5 = new Person();
        $employee5->setFirstName('Sam');
        $employee5->setLastName('Harris');
        $employee5->setDepartment($this->getReference(self::DEPARTMENT_CMS, Department::class));
        $employee5->setPosition($this->getReference(self::POSITION_DEVELOPPEUR, Position::class));
        $employee5->setManager($managerPerson4);
        $employee5->setAlertOnAnswer(false);
        $employee5->setAlertNewRequest(false);
        $employee5->setAlertBeforeVacation(false);
        $manager->persist($employee5);
        $this->addReference('person_harris', $employee5);

        $employee6 = new Person();
        $employee6->setFirstName('Liam');
        $employee6->setLastName('Cooper');
        $employee6->setDepartment($this->getReference(self::DEPARTMENT_CMS, Department::class));
        $employee6->setPosition($this->getReference(self::POSITION_DEVELOPPEUR, Position::class));
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
