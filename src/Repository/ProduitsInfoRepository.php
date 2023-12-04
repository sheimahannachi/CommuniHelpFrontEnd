<?php

namespace App\Repository;

use App\Entity\Listec;
use App\Entity\ProduitsInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitsInfo>
 *
 * @method ProduitsInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitsInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitsInfo[]    findAll()
 * @method ProduitsInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsInfoRepository extends ServiceEntityRepository
{
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitsInfo::class);
    }
    public function findByProductNameAndCancelOrder($productName, EntityManagerInterface $entityManager, ListecRepository $listecRepository)
    {
        // Your existing logic for finding and updating the entity
        $produit = $this->createQueryBuilder('p')
            ->andWhere('p.nomProd = :productName') // Adjust the property name
            ->setParameter('productName', $productName)
            ->getQuery()
            ->getOneOrNullResult();

        if ($produit instanceof ProduitsInfo) {
            // Change the status of the product in ProduitsInfo
            $produit->setStatutProd('available');

            // Find the corresponding product in Listec
            $listec = $listecRepository->findOneBy(['nomproduit' => $productName]);

            if ($listec instanceof Listec) {
                // Remove the product from Listec
                $entityManager->remove($listec);
                $entityManager->flush();
            }
        }

        return $produit;
    }
    public function findByStatutAndSortOrder($statut, $sortOrder)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.statutProd = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('p.prixProd', $sortOrder)
            ->getQuery()
            ->getResult();
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
    public function getAveragePrice()
    {
        return $this->createQueryBuilder('p')
            ->select('AVG(p.prixProd) as averagePrice')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopNProducts($limit)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.prixProd', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getTotalNumberOfProducts()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as totalProducts')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function getProductPricesOverTime(): array
    {
        $repository = $this->entityManager->getRepository('App\Entity\ProduitsInfo');

        $result = $repository->createQueryBuilder('p')
            ->select('p.prixProd, p.createdAt')
            ->getQuery()
            ->getResult();

        $data = [];

        foreach ($result as $row) {
            $data[] = [
                'x' => $row['createdAt'],
                'y' => $row['prixProd'],
            ];
        }

        return $data;
    }
   

    
    public function getProductsByStatus(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT p
            FROM App\Entity\ProduitsInfo p
            WHERE p.statutProd = :status'
        )->setParameter('status', 'available');

        return $query->getResult();
    }
    
//    /**
//     * @return ProduitsInfo[] Returns an array of ProduitsInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProduitsInfo
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }




}
