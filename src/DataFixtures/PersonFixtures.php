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
        $department = $manager->getRepository(Department::class)->findOneBy(['name' => 'IT']);
        $developerPosition = $manager->getRepository(Position::class)->findOneBy(['name' => 'Developer']);
        $managerPosition = $manager->getRepository(Position::class)->findOneBy(['name' => 'Manager']);

        // Create a manager
        $managerPerson = new Person();
        $managerPerson->setFirstName('Jane');
        $managerPerson->setLastName('Smith');
        $managerPerson->setDepartment($department);
        $managerPerson->setPosition($managerPosition); // Assign the Manager position to Jane
        $managerPerson->setAlertOnAnswer(true);
        $managerPerson->setAlertNewRequest(true);
        $managerPerson->setAlertBeforeVacation(true);
        $manager->persist($managerPerson);

        // Create an employee and assign the manager
        $employee = new Person();
        $employee->setFirstName('John');
        $employee->setLastName('Doe');
        $employee->setDepartment($department);
        $employee->setPosition($developerPosition); // Assign the Developer position to John
        $employee->setManager($managerPerson); // Assign Jane as the manager
        $employee->setAlertOnAnswer(true);
        $employee->setAlertNewRequest(true);
        $employee->setAlertBeforeVacation(true);
        $manager->persist($employee);

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