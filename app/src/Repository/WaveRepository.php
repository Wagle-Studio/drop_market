<?php

namespace App\Repository;

use App\Entity\Wave;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wave>
 */
class WaveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wave::class);
    }

    /**
     * @return array<int, Wave>
     */
    public function findNextThreeWavesForShop(int $shopId): array
    {
        return $this->createQueryBuilder("r")
            ->join("r.status", "s")
            ->where("r.shop = :shopId")
            ->andWhere("r.start > :now")
            ->andWhere("s.const = 'PUBLISHED'")
            ->setParameter("shopId", $shopId)
            ->setParameter("now", new \DateTime())
            ->orderBy("r.start", "ASC")
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}
