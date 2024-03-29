<?php

namespace App\Repository;

use App\Entity\OnlineCall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OnlineCall|null find($id, $lockMode = null, $lockVersion = null)
 * @method OnlineCall|null findOneBy(array $criteria, array $orderBy = null)
 * @method OnlineCall[]    findAll()
 * @method OnlineCall[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OnlineCallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OnlineCall::class);
    }

    public function getTotalCnt() {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c.id)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getAgeCounts() {
        $qb =  $this->createQueryBuilder('c')
            ->select('c.age')
            ->addSelect("COUNT(c) as cnt")
            ->groupBy('c.age');

        return $qb->getQuery()->execute();
    }

    // /**
    //  * @return OnlineCall[] Returns an array of OnlineCall objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OnlineCall
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
