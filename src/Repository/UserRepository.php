<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByEmail($email): ?user
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByManagerDepartment($manager, $department){
        return $this->createQueryBuilder('user') 
            ->join('user.person', 'person') 
            ->where('person.manager = :manager') 
            ->andWhere('person.department = :department')
            ->setParameter('manager', $manager) 
            ->setParameter('department', $department); 
    }

    public function findByManager($manager)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.person', 'p')
            ->andWhere('p.manager = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();
    }

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
                    $totalDays++;
                }
            }
        }

        return $totalDays;
    }
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
