<?php

namespace App\DataFixtures;

use App\Entity\Request;
use App\Entity\RequestType;
use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RequestFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupération des types de demandes
        $requestTypeLeave = $manager->getRepository(RequestType::class)->findOneBy(['name' => 'Congé']);
        $requestTypeSick = $manager->getRepository(RequestType::class)->findOneBy(['name' => 'Congé maladie']);
        
        // Récupération des collaborateurs
        $collaboratorJohn = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'John', 'lastName' => 'Doe']);
        $collaboratorAlice = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'Alice', 'lastName' => 'Johnson']);
        $collaboratorBob = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'Bob', 'lastName' => 'Martin']);
        
        // Création d'une demande de congé pour John Doe
        $request1 = new Request();
        $request1->setRequestType($requestTypeLeave);
        $request1->setCollaborator($collaboratorJohn);
        $request1->setStartAt(new \DateTimeImmutable('2025-01-02 08:00:00'));
        $request1->setEndAt(new \DateTimeImmutable('2025-01-10 18:00:00'));
        $request1->setCreatedAt(new \DateTimeImmutable('2024-12-15 14:57:10'));
        $request1->setReceiptFile('');
        $request1->setComment('Congé annuel prévu pour la période de janvier.');
        $request1->setAnswerComment('Demande de congé approuvée.');
        $request1->setAnswer(1);
        $request1->setAnswerAt(new \DateTimeImmutable());
        $manager->persist($request1);

        // Création d'une demande de maladie pour Alice Johnson
        $request2 = new Request();
        $request2->setRequestType($requestTypeSick);
        $request2->setCollaborator($collaboratorAlice);
        $request2->setStartAt(new \DateTimeImmutable('2025-02-01 08:00:00'));
        $request2->setEndAt(new \DateTimeImmutable('2025-02-03 18:00:00'));
        $request2->setCreatedAt(new \DateTimeImmutable('2025-01-30 09:30:00'));
        $request2->setReceiptFile('');
        $request2->setComment('Maladie, besoin de repos pour quelques jours.');
        $request2->setAnswerComment('Demande de maladie approuvée.');
        $request2->setAnswer(1);
        $request2->setAnswerAt(new \DateTimeImmutable());
        $manager->persist($request2);

        // Création d'une demande de congé pour Bob Martin
        $request3 = new Request();
        $request3->setRequestType($requestTypeLeave);
        $request3->setCollaborator($collaboratorBob);
        $request3->setStartAt(new \DateTimeImmutable('2025-03-15 08:00:00'));
        $request3->setEndAt(new \DateTimeImmutable('2025-03-20 18:00:00'));
        $request3->setCreatedAt(new \DateTimeImmutable('2025-03-01 13:45:00'));
        $request3->setReceiptFile('');
        $request3->setComment('Congé prévu pour des vacances personnelles.');
        $request3->setAnswerComment('Demande de congé en attente.');
        $request3->setAnswer(0); // 0 pour une réponse en attente
        $request3->setAnswerAt(new \DateTimeImmutable()); // Pas encore répondu
        $manager->persist($request3);

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
