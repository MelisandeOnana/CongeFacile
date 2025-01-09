<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DepartmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Sales'];

        foreach ($departments as $deptName) {
            $department = new Department();
            $department->setName($deptName);
            $manager->persist($department);
        }

        $manager->flush();
    }
}