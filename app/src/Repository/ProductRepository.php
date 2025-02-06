<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Returns products matching showcase filters.
     *
     * @return Product[]
     */
    public function showcaseFilters(string $shopId, string $title): array
    {
        $queryBuilder = $this->createQueryBuilder("p")
            ->andWhere("p.shop = :shopId")
            ->setParameter("shopId", $shopId);

        if ($title) {
            $queryBuilder->andWhere("p.title LIKE :title")
                ->setParameter("title", "%" . $title . "%");
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }
}
