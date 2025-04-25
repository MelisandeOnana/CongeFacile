<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class DepartmentFixtures extends Fixture
{
    private const DEPARTMENT_SYMFONY = 'Symfony';
    private const DEPARTMENT_POLE_UX = 'Pôle_ux';
    private const DEPARTMENT_MARKETING = 'Marketing';
    private const DEPARTMENT_CMS = 'CMS';

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

            // Ajout de la référence pour chaque département
            $this->addReference($deptName, $department);
        }

        $manager->flush();
    }
}
