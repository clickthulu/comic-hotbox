<?php

namespace App\Repository;

use App\Entity\HotBox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HotBox>
 *
 * @method HotBox|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotBox|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotBox[]    findAll()
 * @method HotBox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotBoxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotBox::class);
    }

//    /**
//     * @return HotBox[] Returns an array of HotBox objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HotBox
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
