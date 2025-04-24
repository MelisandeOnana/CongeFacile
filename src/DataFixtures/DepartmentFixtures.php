<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class DepartmentFixtures extends Fixture
{
    private const DEPARTMENT_SYMFONY = 'Symfony';
    private const DEPARTMENT_CMS = 'CMS';
    private const DEPARTMENT_POLE_UX = 'Pôle UX';
    private const DEPARTMENT_MARKETING = 'Marketing';

    public function load(ObjectManager $manager): void
    {
        $departments = [
            self::DEPARTMENT_SYMFONY,
            self::DEPARTMENT_CMS,
            self::DEPARTMENT_POLE_UX,
            self::DEPARTMENT_MARKETING,
        ];

        foreach ($departments as $deptName) {
            $department = new Department();
            $department->setName($deptName);
            $manager->persist($department);

            $this->addReference('department_' . strtolower(str_replace(' ', '_', $deptName)), $department);
        }

        $manager->flush();
    }
}
