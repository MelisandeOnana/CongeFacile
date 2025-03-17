<?php

namespace App\DataFixtures;

use App\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PositionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $positions = ['Developpeur', 'Manager', 'Commercial', 'Designeur', 'DevOps'];

        foreach ($positions as $positionName) {
            $position = new Position();
            $position->setName($positionName);
            $manager->persist($position);

            $this->addReference('position_' . strtolower(str_replace(' ', '_', $positionName)), $position);
        }

        $manager->flush();
    }
}