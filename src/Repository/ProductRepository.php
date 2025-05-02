<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * @return Product[] Returns an array of active Products
     */
    public function findActiveProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :val')
            ->setParameter('val', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * Trouve des produits similaires (même catégorie) à l'exception du produit courant
     * 
     * @param Product $product Le produit pour lequel chercher des similaires
     * @param int $limit Nombre maximum de produits à retourner
     * @return Product[] Returns an array of similar Products
     */
    public function findSimilarProducts(Product $product, int $limit = 4): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->andWhere('p.id != :id')
            ->andWhere('p.isActive = :active')
            ->setParameter('category', $product->getCategory())
            ->setParameter('id', $product->getId())
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}