<?php

namespace App\Repository;

use App\Entity\Etude;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\Climat;
use App\Entity\Expert;

/**
 * @extends ServiceEntityRepository<Etude>
 */
class EtudeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etude::class);
    }

   // In EtudeRepository.php

// In EtudeRepository.php

public function findByCultureIn($cultures): array
{
    return $this->createQueryBuilder('e')
        ->innerJoin('e.culture', 'c')  // Join with the Culture entity
        ->andWhere('c IN (:cultures)') 
        ->setParameter('cultures', $cultures)
        ->getQuery()
        ->getResult();
}
public function searchEtudes(?Climat $climat, ?Expert $expert, ?int $userId)
{
    $qb = $this->createQueryBuilder('e')
        ->innerJoin('e.culture', 'c')  // Jointure avec Culture
        ->addSelect('c');

    if ($climat) {
        $qb->andWhere('e.climat = :climat')
           ->setParameter('climat', $climat->value);
    }

    if ($expert) {
        $qb->andWhere('e.expert = :expert')
           ->setParameter('expert', $expert);
    }

    if ($userId) {  // 🔥 Filtrer par utilisateur connecté
        $qb->andWhere('e.id_user = :userId')
           ->setParameter('userId', $userId);
    }

    return $qb->getQuery()->getResult();
}



    //    /**
    //     * @return Etude[] Returns an array of Etude objects
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

    //    public function findOneBySomeField($value): ?Etude
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
