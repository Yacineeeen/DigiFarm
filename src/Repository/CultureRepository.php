<?php

namespace App\Repository;

use App\Entity\Culture;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Culture>
 */
class CultureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Culture::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id_user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findCulturesByUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id_user = :user')
            ->setParameter('user', $userId)
            ->select('c.id, c.nom, c.date_plantation AS datePlantation, c.date_recolte AS dateRecolte')
            ->getQuery()
            ->getResult();
    }
}
