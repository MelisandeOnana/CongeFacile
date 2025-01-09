<?php

namespace App\DataFixtures;

use App\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PositionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $positions = ['Developer', 'Manager', 'Analyst', 'Designer', 'Tester'];

        foreach ($positions as $positionName) {
            $position = new Position();
            $position->setName($positionName);
            $manager->persist($position);
        }

        $manager->flush();
    }
}