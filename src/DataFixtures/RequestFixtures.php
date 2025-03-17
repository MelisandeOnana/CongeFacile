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
        
        // Création d'une demande de congé pour John Doe
        $request1 = new Request();
        $request1->setRequestType($this->getReference( 'type_congé_maladie', RequestType::class));
        $request1->setCollaborator($this->getReference( 'person_doe', Person::class));
        $request1->setStartAt(new \DateTimeImmutable('2025-01-06 08:00:00'));
        $request1->setEndAt(new \DateTimeImmutable('2025-01-08 18:00:00'));
        $request1->setCreatedAt(new \DateTimeImmutable('2025-01-05 10:30:19'));
        $request1->setReceiptFile('');
        $request1->setComment('Je suis malade, je ne pourrai pas venir travailler.');
        $request1->setAnswerComment('Demande de congé approuvée.');
        $request1->setAnswer(1);
        $request1->setAnswerAt(new \DateTimeImmutable('2025-01-05 10:23:10'));
        $manager->persist($request1);

        // Création d'une 2eme demande de congé pour John Doe
        $request2 = new Request();
        $request2->setRequestType($this->getReference('type_congé_payé', RequestType::class));
        $request2->setCollaborator($this->getReference( 'person_doe', Person::class));
        $request2->setStartAt(new \DateTimeImmutable('2025-02-10 08:00:00'));
        $request2->setEndAt(new \DateTimeImmutable('2025-02-14 18:00:00'));
        $request2->setCreatedAt(new \DateTimeImmutable('2025-01-10 10:23:10'));
        $request2->setReceiptFile('');
        $request2->setComment('Je souhaite prendre des congés payés.');
        $request2->setAnswerComment('Demande de congé approuvée.');
        $request2->setAnswer(1);
        $request2->setAnswerAt(new \DateTimeImmutable('2025-02-01 14:53:00'));
        $manager->persist($request2);

        // Création d'une demande de congé pour Paul West
        $request3 = new Request();
        $request3->setRequestType($this->getReference('type_congé_sans_solde',RequestType::class));
        $request3->setCollaborator($this->getReference('person_west',Person::class));
        $request3->setStartAt(new \DateTimeImmutable('2025-03-17 08:00:00'));
        $request3->setEndAt(new \DateTimeImmutable('2025-03-19 18:00:00'));
        $request3->setCreatedAt(new \DateTimeImmutable('2025-03-10 19:43:10'));
        $request3->setReceiptFile('');
        $request3->setComment('Je souhaite prendre des congés sans solde.');
        $request3->setAnswerComment('Demande de congé refusée.');
        $request3->setAnswer(2);
        $request3->setAnswerAt(new \DateTimeImmutable('2025-03-11 14:53:00'));
        $manager->persist($request3);

        // Création d'une demande de congé pour Paul West
        $request4 = new Request();
        $request4->setRequestType($this->getReference('type_congé_sans_solde',RequestType::class));
        $request4->setCollaborator($this->getReference('person_west',Person::class));
        $request4->setStartAt(new \DateTimeImmutable('2025-03-20 08:00:00'));
        $request4->setEndAt(new \DateTimeImmutable('2025-03-21 18:00:00'));
        $request4->setCreatedAt(new \DateTimeImmutable('2025-03-11 16:23:10'));
        $request4->setReceiptFile('');
        $request4->setComment('Je souhaite prendre des congés sans solde.');
        $request4->setAnswerComment('Demande de congé approuvée.');
        $request4->setAnswer(1);
        $request4->setAnswerAt(new \DateTimeImmutable('2025-03-12 10:02:00'));
        $manager->persist($request4);

        // Création d'une demande de congé pour Alice Johnson
        $request5 = new Request();
        $request5->setRequestType($this->getReference( 'type_congé_maternité', RequestType::class));
        $request5->setCollaborator($this->getReference('person_johnson', Person::class));
        $request5->setStartAt(new \DateTimeImmutable('2025-04-01 08:00:00'));
        $request5->setEndAt(new \DateTimeImmutable('2025-08-08 18:00:00'));
        $request5->setCreatedAt(new \DateTimeImmutable('2025-02-15 08:56:10'));
        $request5->setReceiptFile('');
        $request5->setComment('Je suis enceinte, je souhaite prendre un congé maternité.');
        $request5->setAnswerComment('Demande de congé approuvée.');
        $request5->setAnswer(1);
        $request5->setAnswerAt(new \DateTimeImmutable('2025-03-01 14:20:02'));
        $manager->persist($request5);

        // Création d'une demande de congé pour Eva Green
        $request6 = new Request();
        $request6->setRequestType($this->getReference('type_congé_payé', RequestType::class));
        $request6->setCollaborator($this->getReference('person_green',Person::class));
        $request6->setStartAt(new \DateTimeImmutable('2025-03-17 08:00:00'));
        $request6->setEndAt(new \DateTimeImmutable('2025-03-21 18:00:00'));
        $request6->setCreatedAt(new \DateTimeImmutable('2025-03-03 08:50:10'));
        $request6->setReceiptFile('');
        $request6->setComment('Je souhaite prendre des congés payés.');
        $request6->setAnswerComment('Demande de congé approuvée.');
        $request6->setAnswer(1);
        $request6->setAnswerAt(new \DateTimeImmutable('2025-03-05 14:20:02'));
        $manager->persist($request6);

        // Création d'une demande de congé pour Eva Green
        $request7 = new Request();
        $request7->setRequestType($this->getReference('type_congé_payé', RequestType::class));
        $request7->setCollaborator($this->getReference('person_green',Person::class));
        $request7->setStartAt(new \DateTimeImmutable('2025-04-07 08:00:00'));
        $request7->setEndAt(new \DateTimeImmutable('2025-04-11 18:00:00'));
        $request7->setCreatedAt(new \DateTimeImmutable('2025-03-03 08:52:58'));
        $request7->setReceiptFile('');
        $request7->setComment('Je souhaite prendre des congés payés.');
        $request7->setAnswerComment('Demande de congé refusée.');
        $request7->setAnswer(2);
        $request7->setAnswerAt(new \DateTimeImmutable('2025-03-05 14:20:53'));
        $manager->persist($request7);

        // Création d'une demande de congé pour Tom Phillips
        $request8 = new Request();
        $request8->setRequestType($this->getReference('type_congé_paternité',RequestType::class));
        $request8->setCollaborator($this->getReference('person_phillips',Person::class));
        $request8->setStartAt(new \DateTimeImmutable('2025-06-16 08:00:00'));
        $request8->setEndAt(new \DateTimeImmutable('2025-06-27 18:00:00'));
        $request8->setCreatedAt(new \DateTimeImmutable('2025-03-30 18:28:09'));
        $request8->setReceiptFile('');
        $request8->setComment('Je vais etre père, je souhaite prendre un congé paternité.');
        $request8->setAnswerComment('Demande de congé approuvée.');
        $request8->setAnswer(1);
        $request8->setAnswerAt(new \DateTimeImmutable('2025-04-01 16:09:32'));
        $manager->persist($request8);

        // Création d'une demande de congé pour Sam Harris
        $request9 = new Request();
        $request9->setRequestType($this->getReference('type_congé_payé', RequestType::class));
        $request9->setCollaborator($this->getReference('person_harris',Person::class));
        $request9->setStartAt(new \DateTimeImmutable('2025-07-21 08:00:00'));
        $request9->setEndAt(new \DateTimeImmutable('2025-08-08 18:00:00'));
        $request9->setCreatedAt(new \DateTimeImmutable('2025-04-02 09:40:37'));
        $request9->setReceiptFile('');
        $request9->setComment('Je souhaite prendre des congés payés.');
        $request9->setAnswerComment('Demande de congé approuvée.');
        $request9->setAnswer(1);
        $request9->setAnswerAt(new \DateTimeImmutable('2025-04-03 19:10:52'));
        $manager->persist($request9);

        // Création d'une demande de congé pour Liam Cooper
        $request10 = new Request();
        $request10->setRequestType($this->getReference('type_congé_payé', RequestType::class));
        $request10->setCollaborator($this->getReference( 'person_cooper',Person::class));
        $request10->setStartAt(new \DateTimeImmutable('2025-08-11 08:00:00'));
        $request10->setEndAt(new \DateTimeImmutable('2025-08-29 18:00:00'));
        $request10->setCreatedAt(new \DateTimeImmutable('2025-04-10 19:56:27'));
        $request10->setReceiptFile('');
        $request10->setComment('Je souhaite prendre des congés payés.');
        $request10->setAnswerComment('Demande de congé approuvée.');
        $request10->setAnswer(1);
        $request10->setAnswerAt(new \DateTimeImmutable('2025-04-11 09:02:49'));
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
