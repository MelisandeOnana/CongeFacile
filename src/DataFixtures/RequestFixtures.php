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
        $requestType = $manager->getRepository(RequestType::class)->findOneBy(['name' => 'Congé']);
        $collaborator = $manager->getRepository(Person::class)->findOneBy(['firstName' => 'John', 'lastName' => 'Doe']);

        $request = new Request();
        $request->setRequestType($requestType);
        $request->setCollaborator($collaborator);
        $request->setStartAt(new \DateTimeImmutable('2025-01-02 08:00:00'));
        $request->setEndAt(new \DateTimeImmutable('2025-01-10 18:00:00'));
        $request->setCreatedAt(new \DateTimeImmutable('2024-12-15 14:57:10'));
        $request->setReceiptFile('receipt.pdf');
        $request->setComment('Ceci est un commentaire.');
        $request->setAnswerComment('Ceci est un commentaire de réponse.');
        $request->setAnswer(1);
        $request->setAnswerAt(new \DateTimeImmutable());

        $manager->persist($request);
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