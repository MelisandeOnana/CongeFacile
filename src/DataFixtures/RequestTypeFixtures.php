<?php

namespace App\DataFixtures;

use App\Entity\RequestType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RequestTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $requestTypes = ['Congé payé', 'Congé maladie', 'Congé maternité', 'Congé paternité', 'Congé sans solde'];

        foreach ($requestTypes as $typeName) {
            $requestType = new RequestType();
            $requestType->setName($typeName);
            $manager->persist($requestType);

            $this->addReference('type_' . strtolower(str_replace(' ', '_', $typeName)), $requestType);
        }

        $manager->flush();
    }
}
