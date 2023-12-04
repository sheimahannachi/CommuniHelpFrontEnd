<?php

namespace App\Repository;

use App\Entity\LivraisonP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LivraisonP>
 *
 * @method LivraisonP|null find($id, $lockMode = null, $lockVersion = null)
 * @method LivraisonP|null findOneBy(array $criteria, array $orderBy = null)
 * @method LivraisonP[]    findAll()
 * @method LivraisonP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivraisonPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivraisonP::class);
    }
   

     /**
     * Recherche les produits par nom.
     *
     * @param string $productName
     * @return ProduitsInfo[] Returns an array of ProduitsInfo objects
     */
    public function findByNomProd($productName)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nomProd LIKE :productName')
            ->setParameter('productName', '%' . $productName . '%')
            ->getQuery()
            ->getResult();
    }

    // Dans votre repository ProduitsInfoRepository
public function searchProducts($productName)
{
    $queryBuilder = $this->createQueryBuilder('p')
        ->where('p.nomProd LIKE :productName')
        ->setParameter('productName', '%' . $productName . '%')
        ->getQuery();

    return $queryBuilder->getResult();
}




//    /**
//     * @return LivraisonP[] Returns an array of LivraisonP objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LivraisonP
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
