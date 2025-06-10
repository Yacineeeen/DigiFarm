<?php

namespace App\Repository;

use App\Entity\Machine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Machine>
 */
class MachineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Machine::class);
    }
    public function findTotalMaintenanceCostPerMachine(): array
{
    return $this->createQueryBuilder('m')
        ->select('m.nom', 'SUM(mt.cout) AS total_cost')
        ->leftJoin('m.Maintenance', 'mt')
        ->groupBy('m.id')
        ->getQuery()
        ->getResult();
}
public function findMaintenanceFrequency(): array
{
    return $this->createQueryBuilder('m')
        ->select('m.nom', 'COUNT(mt.id) AS maintenance_count')
        ->leftJoin('m.Maintenance', 'mt')
        ->groupBy('m.id')
        ->getQuery()
        ->getResult();
}

//    /**
//     * @return Machine[] Returns an array of Machine objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Machine
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
