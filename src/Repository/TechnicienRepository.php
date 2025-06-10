<?php

namespace App\Repository;

use App\Entity\Technicien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Technicien>
 */
class TechnicienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technicien::class);
    }
    public function findNearestTechnicien(float $latitude, float $longitude): ?Technicien
    {
        $techniciens = $this->findAll();
        $nearestTechnicien = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($techniciens as $technicien) {
            $techLatitude = $technicien->getLatitude();
            $techLongitude = $technicien->getLongitude();

            if ($techLatitude && $techLongitude) {
                $distance = $this->calculateDistance($latitude, $longitude, $techLatitude, $techLongitude);

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestTechnicien = $technicien;
                }
            }
        }

        return $nearestTechnicien;
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth radius in kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    //    /**
    //     * @return Technicien[] Returns an array of Technicien objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Technicien
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
