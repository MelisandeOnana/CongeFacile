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
        $position = $manager->getRepository(Position::class)->findOneBy(['name' => 'Developer']);

        // Create a manager
        $managerPerson = new Person();
        $managerPerson->setFirstName('Jane');
        $managerPerson->setLastName('Smith');
        $managerPerson->setDepartment($department);
        $managerPerson->setPosition($position);
        $managerPerson->setAlertOnAnswer(true);
        $managerPerson->setAlertNewRequest(true);
        $managerPerson->setAlertBeforeVacation(true);
        $manager->persist($managerPerson);

        // Create an employee and assign the manager
        $employee = new Person();
        $employee->setFirstName('John');
        $employee->setLastName('Doe');
        $employee->setDepartment($department);
        $employee->setPosition($position);
        $employee->setManager($managerPerson);
        $employee->setAlertOnAnswer(true);
        $employee->setAlertNewRequest(true);
        $employee->setAlertBeforeVacation(true);
        $manager->persist($employee);

        $manager->flush();
    }

    public function getDependencies():array
    {
        return [
            DepartmentFixtures::class,
            PositionFixtures::class,
        ];
    }
}