<?php

namespace App\Repository;

use App\Entity\Rotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rotation>
 *
 * @method Rotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rotation[]    findAll()
 * @method Rotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rotation::class);
    }

//    /**
//     * @return Rotation[] Returns an array of Rotation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Rotation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
