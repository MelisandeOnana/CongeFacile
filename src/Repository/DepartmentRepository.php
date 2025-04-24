<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Department>
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    /**
     * Recherche des départements par nom.
     *
     * @param string $search
     * @return Query Retourne une Query pour la pagination
     */
    public function findBySearch(string $search): Query
    {
        return $this->createQueryBuilder('d')
            ->where('d.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery();
    }

    /**
     * Récupère tous les départements triés par ID décroissant.
     *
     * @return Department[] Retourne un tableau d'objets Department
     */
    public function findAllOrderedByNewest(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Department[] Returns an array of Department objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Department
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
