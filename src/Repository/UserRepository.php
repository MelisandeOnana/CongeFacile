<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Trouve un utilisateur par son email.
     *
     * @param string $email L'email de l'utilisateur.
     * @return User|null Retourne l'utilisateur correspondant ou null s'il n'existe pas.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve les utilisateurs d'un département géré par un manager spécifique.
     *
     * @param User $manager Le manager responsable.
     * @param string $department Le département à filtrer.
     * @return array Retourne une liste d'utilisateurs.
     */
    public function findByManagerDepartment(User $manager, string $department): array
    {
        return $this->createQueryBuilder('user')
            ->join('user.person', 'person')
            ->where('person.manager = :manager')
            ->andWhere('person.department = :department')
            ->setParameter('manager', $manager)
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les utilisateurs gérés par un manager spécifique.
     *
     * @param User $manager Le manager responsable.
     * @return array Retourne une liste d'utilisateurs.
     */
    public function findByManager(User $manager): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.person', 'p')
            ->andWhere('p.manager = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le nombre total de jours de congé pris par un utilisateur pour une année donnée.
     *
     * @param User $user L'utilisateur concerné.
     * @param int $year L'année pour laquelle calculer les jours de congé.
     * @return int Retourne le nombre total de jours de congé.
     */
    public function getVacationDaysForYear(User $user, int $year): int
    {
        $startDate = new \DateTime("$year-01-01");
        $endDate = new \DateTime("$year-12-31");

        $qb = $this->createQueryBuilder('u')
            ->select('r.startAt, r.endAt')
            ->innerJoin('App\Entity\Request', 'r', 'WITH', 'r.collaborator = u.person')
            ->where('u.id = :userId')
            ->andWhere('r.startAt BETWEEN :startDate AND :endDate')
            ->setParameter('userId', $user->getId())
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery();

        $requests = $qb->getResult();

        $totalDays = 0;
        foreach ($requests as $request) {
            $startAt = $request['startAt'];
            $endAt = $request['endAt'];
            $period = new \DatePeriod($startAt, new \DateInterval('P1D'), $endAt->modify('+1 day'));

            foreach ($period as $date) {
                if ($date->format('N') < 6) { // 6 et 7 sont samedi et dimanche
                    ++$totalDays;
                }
            }
        }

        return $totalDays;
    }

    /**
     * Recherche les membres d'une équipe en fonction de critères spécifiques.
     *
     * @param array $criteria Les critères de recherche (nom, prénom, email, etc.).
     * @param User $manager Le manager responsable.
     * @param string $department Le département à filtrer.
     * @return array Retourne une liste d'utilisateurs correspondant aux critères.
     */
    public function findTeamMembersQuery(array $criteria, User $manager, Department $department): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->join('u.person', 'p')
            ->leftJoin('p.position', 'pos')
            ->where('p.manager = :manager')
            ->andWhere('p.department = :department')
            ->setParameter('manager', $manager)
            ->setParameter('department', $department); // Utilisation de l'objet Department directement

        if (!empty($criteria['lastName'])) {
            $queryBuilder->andWhere('p.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $criteria['lastName'] . '%');
        }

        if (!empty($criteria['firstName'])) {
            $queryBuilder->andWhere('p.firstName LIKE :firstName')
                ->setParameter('firstName', '%' . $criteria['firstName'] . '%');
        }

        if (!empty($criteria['email'])) {
            $queryBuilder->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $criteria['email'] . '%');
        }

        if (!empty($criteria['position'])) {
            $queryBuilder->andWhere('pos.name LIKE :position')
                ->setParameter('position', '%' . $criteria['position'] . '%');
        }

        $results = $queryBuilder->getQuery()->getResult();

        if (isset($criteria['totalVacationDays'])) {
            $filteredResults = [];
            foreach ($results as $user) {
                $totalVacationDays = $this->getVacationDaysForYear($user, (int) date('Y'));
                if ($totalVacationDays == (int) $criteria['totalVacationDays']) {
                    $filteredResults[] = $user;
                }
            }
            return $filteredResults;
        }

        return $results;
    }
}
