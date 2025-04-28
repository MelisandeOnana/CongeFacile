<?php

namespace App\Repository;

use App\Entity\Request;
use App\Entity\Person;
use App\Entity\RequestType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    private PersonRepository $personRepository;

    public function __construct(ManagerRegistry $registry, PersonRepository $personRepository)
    {
        parent::__construct($registry, Request::class);
        $this->personRepository = $personRepository;
    }

    /**
     * Trouve les requêtes pour une date donnée.
     *
     * @param \DateTime $date
     * @return Request[] Retourne un tableau d'objets Request
     */
    public function findRequestsWithDate(\DateTime $date): array
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

    /**
     * Compte les requêtes en attente pour un manager donné.
     *
     * @param Person $manager
     * @return int Retourne le nombre de requêtes
     */
    public function countPendingRequestsByManager(Person $manager): int
    {
        $persons = $this->personRepository->getPersonByManager($manager);

        return (int) $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.collaborator IN (:persons)')
            ->andWhere('r.answer = :status')
            ->setParameter('persons', $persons)
            ->setParameter('status', 3)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte les requêtes par type de requête.
     *
    * @param RequestType $requestType
    * @return int Retourne le nombre de requêtes
    */
    public function countRequestsByRequestType(RequestType $requestType): int
    {
        $startOfYear = (new \DateTime())->setDate((int)date('Y'), 1, 1)->setTime(0, 0, 0);
        $endOfYear = (new \DateTime())->setDate((int)date('Y'), 12, 31)->setTime(23, 59, 59);

        return (int) $this->createQueryBuilder('r')
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

    /**
     * Trouve les requêtes par mois de réponse.
     *
     * @param \DateTime $date
     * @return Request[] Retourne un tableau d'objets Request
     */
    public function findRequestsByMonthOfAnswer(\DateTime $date): array
    {
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

    /**
     * Trouve les requêtes filtrées selon plusieurs critères.
     *
     * @param array $collaborators
     * @param string|null $filterType
     * @param string|null $filterDate
     * @param string|null $filterStart
     * @param string|null $filterEnd
     * @param int|null $filterNumber
     * @param Person|null $filterCollaborator
     * @return Request[] Retourne un tableau d'objets Request
     */
    public function findFilteredRequests(
        array $collaborators,
        ?string $filterType,
        ?string $filterDate,
        ?string $filterStart,
        ?string $filterEnd,
        ?int $filterNumber,
        ?Person $filterCollaborator
    ): array {
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

    /**
     * Regroupe les requêtes par mois.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array Retourne un tableau contenant les mois et le nombre de requêtes
     */
    public function findRequestsGroupedByMonth(\DateTime $startDate, \DateTime $endDate): array
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
}
