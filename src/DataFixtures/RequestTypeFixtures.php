<?php

namespace App\DataFixtures;

use App\Entity\RequestType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class RequestTypeFixtures extends Fixture
{
    private const TYPE_CONGE_PAYE = 'type_conge_paye';
    private const TYPE_CONGE_MALADIE = 'type_conge_maladie';
    private const TYPE_CONGE_MATERNITE = 'type_conge_maternite';
    private const TYPE_CONGE_PATERNITE = 'type_conge_paternite';
    private const TYPE_CONGE_SANS_SOLDE = 'type_conge_sans_solde';

    public function load(ObjectManager $manager): void
    {
        $requestTypes = [
            self::TYPE_CONGE_PAYE => 'Congé payé',
            self::TYPE_CONGE_MALADIE => 'Congé maladie',
            self::TYPE_CONGE_MATERNITE => 'Congé maternité',
            self::TYPE_CONGE_PATERNITE => 'Congé paternité',
            self::TYPE_CONGE_SANS_SOLDE => 'Congé sans solde',
        ];

        foreach ($requestTypes as $constant => $typeName) {
            $requestType = new RequestType();
            $requestType->setName($typeName);
            $manager->persist($requestType);

            $this->addReference($constant, $requestType);
        }

        $manager->flush();
    }
}
