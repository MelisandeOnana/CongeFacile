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
            ->where('r.collaborator IN (:persons)')
            ->andWhere('r.answer = :status')
            ->setParameter('persons', $persons)
            ->setParameter('status', 3)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    public function countRequestsByRequestType($requestType)
    {
        $startOfYear = (new \DateTime())->setDate((int)date('Y'), 1, 1)->setTime(0, 0, 0);
        $endOfYear = (new \DateTime())->setDate((int)date('Y'), 12, 31)->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.requestType = :requestType')
            ->andWhere('r.startAt >= :startOfYear')
            ->andWhere('r.startAt <= :endOfYear')
            ->setParameter('requestType', $requestType)
            ->setParameter('startOfYear', $startOfYear)
            ->setParameter('endOfYear', $endOfYear)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findRequestsByMonthOfAnswer(\DateTime $date) {
        $startOfMonth = (clone $date)->setDate((int)$date->format('Y'), (int)$date->format('m'), 1)->setTime(0, 0, 0);
        $endOfMonth = (clone $date)->setDate((int)$date->format('Y'), (int)$date->format('m'), (int)$date->format('t'))->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            ->where('r.answerAt >= :startOfMonth')
            ->andWhere('r.answerAt <= :endOfMonth')
            ->setParameter('startOfMonth', $startOfMonth)
            ->setParameter('endOfMonth', $endOfMonth)
            ->getQuery()
            ->getResult();
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
