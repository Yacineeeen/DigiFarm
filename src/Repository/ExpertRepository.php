<?php

namespace App\Repository;

use App\Entity\Expert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\Dispo;

/**
 * @extends ServiceEntityRepository<Expert>
 */
class ExpertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expert::class);
    }

    public function findAvailableExperts()
    {
        return $this->createQueryBuilder('e')
            ->where('e.dispo = :disponible')
            ->setParameter('disponible', Dispo::DISPONIBLE->value);
    }
    //    /**
    //     * @return Expert[] Returns an array of Expert objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Expert
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
