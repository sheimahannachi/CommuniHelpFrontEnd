<?php

namespace App\Repository;

use App\Entity\Donationforms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Donationforms>
 *
 * @method Donationforms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donationforms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donationforms[]    findAll()
 * @method Donationforms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonationformsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donationforms::class);
    }

//    /**
//     * @return Donationforms[] Returns an array of Donationforms objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Donationforms
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
