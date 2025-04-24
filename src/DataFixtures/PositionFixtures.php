<?php

namespace App\DataFixtures;

use App\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class PositionFixtures extends Fixture
{
    private const POSITION_DEVELOPPEUR = 'Developpeur';
    private const POSITION_MANAGER = 'Manager';
    private const POSITION_COMMERCIAL = 'Commercial';
    private const POSITION_DESIGNEUR = 'Designeur';
    private const POSITION_DEVOPS = 'DevOps';

    public function load(ObjectManager $manager): void
    {
        $positions = [
            self::POSITION_DEVELOPPEUR,
            self::POSITION_MANAGER,
            self::POSITION_COMMERCIAL,
            self::POSITION_DESIGNEUR,
            self::POSITION_DEVOPS,
        ];

        foreach ($positions as $positionName) {
            $position = new Position();
            $position->setName($positionName);
            $manager->persist($position);

            $this->addReference('position_' . strtolower(str_replace(' ', '_', $positionName)), $position);
        }

        $manager->flush();
    }
}
