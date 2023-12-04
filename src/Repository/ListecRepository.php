<?php

namespace App\Repository;

use App\Entity\Listec;
use App\Entity\ProduitsInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Listec>
 *
 * @method Listec|null find($id, $lockMode = null, $lockVersion = null)
 * @method Listec|null findOneBy(array $criteria, array $orderBy = null)
 * @method Listec[]    findAll()
 * @method Listec[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListecRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Listec::class);
    }
    // In ProduitsInfoRepository.php
public function findByProductNameAndCancelOrder($productName, EntityManagerInterface $entityManager, ListecRepository $listecRepository)
{
    // Find the product in ProduitsInfo based on its name
    $produit = $this->createQueryBuilder('p')
        ->andWhere('p.nomproduit = :productName')
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

public function getTotalNumberOfLists()
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id) as totalLists')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function getListecDistribution(): array
    {
        $repository = $this->entityManager->getRepository('App\Entity\Listec');

        $result = $repository->createQueryBuilder('l')
            ->select('l.nomproduit, COUNT(l.id) as count')
            ->groupBy('l.nomproduit')
            ->getQuery()
            ->getResult();

        $data = [];

        foreach ($result as $row) {
            $data[$row['nomproduit']] = $row['count'];
        }

        return $data;
    }
//     * @return Listec[] Returns an array of Listec objects
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

//    public function findOneBySomeField($value): ?Listec
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
