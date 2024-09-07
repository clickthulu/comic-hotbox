<?php

namespace App\Repository;

use App\Entity\Webring;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Webring>
 *
 * @method Webring|null find($id, $lockMode = null, $lockVersion = null)
 * @method Webring|null findOneBy(array $criteria, array $orderBy = null)
 * @method Webring[]    findAll()
 * @method Webring[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebringRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Webring::class);
    }

//    /**
//     * @return Webring[] Returns an array of Webring objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Webring
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
