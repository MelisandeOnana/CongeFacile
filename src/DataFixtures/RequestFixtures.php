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
        $request->setStartAt(new \DateTimeImmutable('2023-01-01'));
        $request->setEndAt(new \DateTimeImmutable('2023-01-10'));
        $request->setCreatedAt(new \DateTimeImmutable());
        $request->setReceiptFile('receipt.pdf');
        $request->setComment('Ceci est un commentaire.');
        $request->setAnswerComment('Ceci est un commentaire de réponse.');
        $request->setAnswer(true);
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