<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DepartmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $departments = ['Symfony', 'CMS', 'Pôle UX', 'Marketing'];

        foreach ($departments as $deptName) {
            $department = new Department();
            $department->setName($deptName);
            $manager->persist($department);

            $this->addReference('department_' . strtolower(str_replace(' ', '_', $deptName)), $department);
        }

        $manager->flush();
    }
}
