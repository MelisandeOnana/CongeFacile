<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PersonRepository;

/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    private $personRepository;

    public function __construct(ManagerRegistry $registry, PersonRepository $personRepository)
    {
        parent::__construct($registry, Request::class);
        $this->personRepository = $personRepository;
    }
    public function findRequestsWithDate(\DateTime $date)
    {
        $startOfDay = (clone $date)->setTime(0, 0, 0);
        $endOfDay = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            ->where('r.startAt >= :startOfDay')
            ->andWhere('r.startAt < :endOfDay')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->getQuery()
            ->getResult();
    }

    public function countPendingRequestsByManager($manager)
    {
        $persons = $this->personRepository->getPersonByManager($manager);

        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.collaborator = :persons')
            ->andWhere('r.answer = :status')
            ->setParameter('persons', $persons)
            ->setParameter('status', 3)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Request[] Returns an array of Request objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Request
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
