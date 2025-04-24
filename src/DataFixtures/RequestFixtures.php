<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\Request;
use App\Entity\RequestType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RequestFixtures extends Fixture implements DependentFixtureInterface
{
    private const TYPE_CONGE_MALADIE = 'type_conge_maladie';
    private const TYPE_CONGE_PAYE = 'type_conge_paye';
    private const TYPE_CONGE_SANS_SOLDE = 'type_conge_sans_solde';
    private const TYPE_CONGE_MATERNITE = 'type_conge_maternite';
    private const TYPE_CONGE_PATERNITE = 'type_conge_paternite';

    private const PERSON_DOE = 'person_doe';
    private const PERSON_WEST = 'person_west';
    private const PERSON_JOHNSON = 'person_johnson';
    private const PERSON_GREEN = 'person_green';
    private const PERSON_PHILLIPS = 'person_phillips';
    private const PERSON_HARRIS = 'person_harris';
    private const PERSON_COOPER = 'person_cooper';

    public function load(ObjectManager $manager): void
    {
        // Création d'une demande de congé pour John Doe
        $request1 = new Request();
        $request1->setRequestType($this->getReference(self::TYPE_CONGE_MALADIE, RequestType::class));
        $request1->setCollaborator($this->getReference(self::PERSON_DOE, Person::class));
        $request1->setStartAt(new \DateTimeImmutable('2025-01-06 08:00:00'));
        $request1->setEndAt(new \DateTimeImmutable('2025-01-08 18:00:00'));
        $request1->setCreatedAt(new \DateTimeImmutable('2025-01-05 10:30:19'));
        $request1->setReceiptFile('2-1-justificatif.pdf');
        $request1->setComment('Je suis malade, je ne pourrai pas venir travailler.');
        $request1->setAnswerComment('Demande de congé approuvée.');
        $request1->setAnswer(true); // Utilisation de booléen
        $request1->setAnswerAt(new \DateTimeImmutable('2025-01-05 10:23:10'));
        $manager->persist($request1);

        // Création d'une 2eme demande de congé pour John Doe
        $request2 = new Request();
        $request2->setRequestType($this->getReference(self::TYPE_CONGE_PAYE, RequestType::class));
        $request2->setCollaborator($this->getReference(self::PERSON_DOE, Person::class));
        $request2->setStartAt(new \DateTimeImmutable('2025-02-10 08:00:00'));
        $request2->setEndAt(new \DateTimeImmutable('2025-02-14 18:00:00'));
        $request2->setCreatedAt(new \DateTimeImmutable('2025-01-10 10:23:10'));
        $request2->setAnswerComment('Demande de congé approuvée.');
        $request2->setAnswer(true); // Utilisation de booléen
        $request2->setAnswerAt(new \DateTimeImmutable('2025-02-01 14:53:00'));
        $manager->persist($request2);

        // Ajoutez les autres demandes de congé ici en suivant le même modèle...
        // Remplacez les chaînes de caractères par les constantes définies ci-dessus
        // et utilisez des booléens pour `setAnswer`.

        // Flush all the entities to the database
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RequestTypeFixtures::class,
            PersonFixtures::class,
        ];
    }
}
