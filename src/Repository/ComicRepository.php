<?php

namespace App\Repository;

use App\Entity\Comic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comic>
 *
 * @method Comic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comic[]    findAll()
 * @method Comic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comic::class);
    }

//    /**
//     * @return Comic[] Returns an array of Comic objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comic
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
