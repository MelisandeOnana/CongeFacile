<?php

namespace App\Repository;

use App\Entity\Person;
use App\Entity\Position;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    /**
     * Récupère les personnes associées à un manager donné.
     *
     * @param Person $manager
     * @return Person[] Retourne un tableau d'objets Person
     */
    public function getPersonByManager(Person $manager): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.manager = :manager_id')
            ->setParameter('manager_id', $manager->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de personnes pour un poste donné.
     *
     * @param Position $position
     * @return int Retourne le nombre de personnes
     */
    public function countByPosition(Position $position): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.position = :position')
            ->setParameter('position', $position)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findManagerByDepartmentId(int $departmentId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.position', 'pos')
            ->where('p.department = :departmentId')
            ->andWhere('pos.name = :positionName')
            ->setParameter('departmentId', $departmentId)
            ->setParameter('positionName', 'Manager')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Person[] Returns an array of Person objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult();
    //    }

    //    public function findOneBySomeField($value): ?Person
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult();
    //    }
}
