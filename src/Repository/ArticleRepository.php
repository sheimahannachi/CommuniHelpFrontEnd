<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }
    public function findBySearchTerm(?string $searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if ($searchTerm !== null) {
            $queryBuilder
                ->andWhere('a.description LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }


//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function getDonsByCriteria(string $ville, string $joursRestants): array
{
    $qb = $this->createQueryBuilder('a')
        ->andWhere('a.ville = :ville')
        ->setParameter('ville', $ville);

    // Add additional criteria based on joursRestants if needed
    if ($joursRestants === '0-10') {
        $qb->andWhere('a.creationDate BETWEEN :startDate AND :endDate')
           ->setParameter('startDate', new \DateTime())
           ->setParameter('endDate', new \DateTime('+10 days'));
    } elseif ($joursRestants === '10-20') {
        $qb->andWhere('a.creationDate BETWEEN :startDate AND :endDate')
           ->setParameter('startDate', new \DateTime('+10 days'))
           ->setParameter('endDate', new \DateTime('+20 days'));
    } elseif ($joursRestants === '20-50') {
        $qb->andWhere('a.creationDate BETWEEN :startDate AND :endDate')
           ->setParameter('startDate', new \DateTime('+20 days'))
           ->setParameter('endDate', new \DateTime('+50 days'));
    } elseif ($joursRestants === '50-150') {
        $qb->andWhere('a.creationDate BETWEEN :startDate AND :endDate')
           ->setParameter('startDate', new \DateTime('+50 days'))
           ->setParameter('endDate', new \DateTime('+150 days'));
    } elseif ($joursRestants === '150-300') {
        $qb->andWhere('a.creationDate BETWEEN :startDate AND :endDate')
           ->setParameter('startDate', new \DateTime('+150 days'))
           ->setParameter('endDate', new \DateTime('+300 days'));
    } elseif ($joursRestants === '300-400') {
        $qb->andWhere('a.creationDate BETWEEN :startDate AND :endDate')
           ->setParameter('startDate', new \DateTime('+300 days'))
           ->setParameter('endDate', new \DateTime('+400 days'));
    } elseif ($joursRestants === '400-600') {
        $qb->andWhere('a.creationDate BETWEEN :startDate AND :endDate')
           ->setParameter('startDate', new \DateTime('+400 days'))
           ->setParameter('endDate', new \DateTime('+600 days'));
    } elseif ($joursRestants === '20-10') {        dump('Calling 50');

        // Add logic for 20-50 days
    }
    // Add similar logic for other joursRestants options...
    dump('Calling getDonsByCriteria');

    $query = $qb->getQuery();
    dump('Calling getDonsByCriteria');
    dump($query->getSQL());
    dump(iterator_to_array($query->getParameters())); // Convert ArrayCollection to array
    return $query->getResult();
    
}
}