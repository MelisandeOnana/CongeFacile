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

    public function findFilteredRequests($collaborators, $filterType, $filterDate, $filterStart, $filterEnd, $filterNumber, $filterCollaborator)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.collaborator IN (:collaborators)')
            ->andWhere('r.answer = :status')
            ->setParameter('collaborators', $collaborators)
            ->setParameter('status', 3);

        if ($filterType) {
            $qb->andWhere('r.requestType = :filterType')
               ->setParameter('filterType', $filterType);
        }

        if ($filterCollaborator) {
            $qb->andWhere('r.collaborator = :filterCollaborator')
               ->setParameter('filterCollaborator', $filterCollaborator);
        }

        if ($filterDate) {
            $startOfDay = (new \DateTimeImmutable($filterDate))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterDate))->setTime(23, 59, 59);
            $qb->andWhere('r.createdAt BETWEEN :startOfDay AND :endOfDay')
               ->setParameter('startOfDay', $startOfDay)
               ->setParameter('endOfDay', $endOfDay);
        }

        if ($filterStart) {
            $startOfDay = (new \DateTimeImmutable($filterStart))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterStart))->setTime(23, 59, 59);
            $qb->andWhere('r.startAt BETWEEN :startOfDay AND :endOfDay')
               ->setParameter('startOfDay', $startOfDay)
               ->setParameter('endOfDay', $endOfDay);
        }

        if ($filterEnd) {
            $startOfDay = (new \DateTimeImmutable($filterEnd))->setTime(0, 0, 0);
            $endOfDay = (new \DateTimeImmutable($filterEnd))->setTime(23, 59, 59);
            $qb->andWhere('r.endAt BETWEEN :startOfDay AND :endOfDay')
               ->setParameter('startOfDay', $startOfDay)
               ->setParameter('endOfDay', $endOfDay);
        }

        if ($filterNumber) {
            $qb->andWhere('r.workingdays = :filterNumber')
               ->setParameter('filterNumber', $filterNumber);
        }

        return $qb->orderBy('r.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    public function findRequestsGroupedByMonth(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->createQueryBuilder('r')
            ->select('SUBSTRING(r.answerAt, 1, 7) as month, COUNT(r.id) as requestCount')
            ->where('r.answerAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
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
