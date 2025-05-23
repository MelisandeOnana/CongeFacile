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
        $request1->setReceiptFile('john_doe_2025-01-05_680f367ac945b.pdf');
        $request1->setComment('Je suis malade, je ne pourrai pas venir travailler.');
        $request1->setAnswerComment('Demande de congé approuvée.');
        $request1->setAnswer(1); // Utilisation de booléen
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
        $request2->setAnswer(1); // Utilisation de booléen
        $request2->setAnswerAt(new \DateTimeImmutable('2025-02-01 14:53:00'));
        $manager->persist($request2);

        $request22 = new Request();
        $request22->setRequestType($this->getReference(self::TYPE_CONGE_PAYE, RequestType::class));
        $request22->setCollaborator($this->getReference(self::PERSON_DOE, Person::class));
        $request22->setStartAt(new \DateTimeImmutable('2025-06-17 08:00:00'));
        $request22->setEndAt(new \DateTimeImmutable('2025-06-21 18:00:00'));
        $request22->setCreatedAt(new \DateTimeImmutable('2025-05-04 10:23:10'));
        $request22->setComment('Je souhaite prendre des congés payés.');
        $request22->setAnswer(3); 
        $manager->persist($request22);

    // Création d'une demande de congé pour Paul West
    $request3 = new Request();
    $request3->setRequestType($this->getReference(self::TYPE_CONGE_SANS_SOLDE, RequestType::class));
    $request3->setCollaborator($this->getReference(self::PERSON_WEST, Person::class));
    $request3->setStartAt(new \DateTimeImmutable('2025-03-17 08:00:00'));
    $request3->setEndAt(new \DateTimeImmutable('2025-03-19 18:00:00'));
    $request3->setCreatedAt(new \DateTimeImmutable('2025-03-10 19:43:10'));
    $request3->setComment('Je souhaite prendre des congés sans solde.');
    $request3->setAnswerComment('Demande de congé refusée.');
    $request3->setAnswer(2);
    $request3->setAnswerAt(new \DateTimeImmutable('2025-03-11 14:53:00'));
    $manager->persist($request3);

    // Création d'une demande de congé pour Paul West
    $request4 = new Request();
    $request4->setRequestType($this->getReference(self::TYPE_CONGE_SANS_SOLDE, RequestType::class));
    $request4->setCollaborator($this->getReference(self::PERSON_WEST, Person::class));
    $request4->setStartAt(new \DateTimeImmutable('2025-03-20 08:00:00'));
    $request4->setEndAt(new \DateTimeImmutable('2025-03-21 18:00:00'));
    $request4->setCreatedAt(new \DateTimeImmutable('2025-03-11 16:23:10'));
    $request4->setAnswerComment('Demande de congé approuvée.');
    $request4->setAnswer(1);
    $request4->setAnswerAt(new \DateTimeImmutable('2025-03-12 10:02:00'));
    $manager->persist($request4);

    // Création d'une demande de congé pour Alice Johnson
    $request5 = new Request();
    $request5->setRequestType($this->getReference(self::TYPE_CONGE_MATERNITE, RequestType::class));
    $request5->setCollaborator($this->getReference(self::PERSON_JOHNSON, Person::class));
    $request5->setStartAt(new \DateTimeImmutable('2025-04-01 08:00:00'));
    $request5->setEndAt(new \DateTimeImmutable('2025-08-08 18:00:00'));
    $request5->setCreatedAt(new \DateTimeImmutable('2025-02-15 08:56:10'));
    $request5->setComment('Je suis enceinte, je souhaite prendre un congé maternité.');
    $request5->setAnswerComment('Demande de congé approuvée.');
    $request5->setAnswer(1);
    $request5->setAnswerAt(new \DateTimeImmutable('2025-03-01 14:20:02'));
    $manager->persist($request5);

    // Création d'une demande de congé pour Eva Green
    $request6 = new Request();
    $request6->setRequestType($this->getReference(self::TYPE_CONGE_PAYE, RequestType::class));
    $request6->setCollaborator($this->getReference(self::PERSON_GREEN, Person::class));
    $request6->setStartAt(new \DateTimeImmutable('2025-03-17 08:00:00'));
    $request6->setEndAt(new \DateTimeImmutable('2025-03-21 18:00:00'));
    $request6->setCreatedAt(new \DateTimeImmutable('2025-03-03 08:50:10'));
    $request6->setComment('Je souhaite prendre des congés payés.');
    $request6->setAnswerComment('Demande de congé approuvée.');
    $request6->setAnswer(1);
    $request6->setAnswerAt(new \DateTimeImmutable('2025-03-05 14:20:02'));
    $manager->persist($request6);

    // Création d'une demande de congé pour Eva Green
    $request7 = new Request();
    $request7->setRequestType($this->getReference(self::TYPE_CONGE_PAYE, RequestType::class));
    $request7->setCollaborator($this->getReference(self::PERSON_GREEN, Person::class));
    $request7->setStartAt(new \DateTimeImmutable('2025-04-07 08:00:00'));
    $request7->setEndAt(new \DateTimeImmutable('2025-04-11 18:00:00'));
    $request7->setCreatedAt(new \DateTimeImmutable('2025-03-03 08:52:58'));
    $request7->setComment('Je souhaite prendre des congés payés.');
    $request7->setAnswerComment('Demande de congé refusée.');
    $request7->setAnswer(2);
    $request7->setAnswerAt(new \DateTimeImmutable('2025-03-05 14:20:53'));
    $manager->persist($request7);

    // Création d'une demande de congé pour Tom Phillips
    $request8 = new Request();
    $request8->setRequestType($this->getReference(self::TYPE_CONGE_PATERNITE, RequestType::class));
    $request8->setCollaborator($this->getReference(self::PERSON_PHILLIPS, Person::class));
    $request8->setStartAt(new \DateTimeImmutable('2025-06-16 08:00:00'));
    $request8->setEndAt(new \DateTimeImmutable('2025-06-27 18:00:00'));
    $request8->setCreatedAt(new \DateTimeImmutable('2025-03-30 18:28:09'));
    $request8->setComment('Je vais etre père, je souhaite prendre un congé paternité.');
    $request8->setAnswerComment('Demande de congé approuvée.');
    $request8->setAnswer(1);
    $request8->setAnswerAt(new \DateTimeImmutable('2025-04-01 16:09:32'));
    $manager->persist($request8);

    // Création d'une demande de congé pour Sam Harris
    $request9 = new Request();
    $request9->setRequestType($this->getReference(self::TYPE_CONGE_PAYE, RequestType::class));
    $request9->setCollaborator($this->getReference(self::PERSON_HARRIS, Person::class));
    $request9->setStartAt(new \DateTimeImmutable('2025-07-21 08:00:00'));
    $request9->setEndAt(new \DateTimeImmutable('2025-08-08 18:00:00'));
    $request9->setCreatedAt(new \DateTimeImmutable('2025-04-02 09:40:37'));
    $request9->setAnswerComment('Demande de congé approuvée.');
    $request9->setAnswer(1);
    $request9->setAnswerAt(new \DateTimeImmutable('2025-04-03 19:10:52'));
    $manager->persist($request9);

    // Création d'une demande de congé pour Liam Cooper
    $request10 = new Request();
    $request10->setRequestType($this->getReference(self::TYPE_CONGE_PAYE, RequestType::class));
    $request10->setCollaborator($this->getReference(self::PERSON_COOPER, Person::class));
    $request10->setStartAt(new \DateTimeImmutable('2025-08-11 08:00:00'));
    $request10->setEndAt(new \DateTimeImmutable('2025-08-29 18:00:00'));
    $request10->setCreatedAt(new \DateTimeImmutable('2025-04-10 19:56:27'));
    $request10->setComment('Je souhaite prendre des congés payés.');
    $request10->setAnswer(3);
    $manager->persist($request10);

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
