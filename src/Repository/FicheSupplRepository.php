<?php

namespace App\Repository;

use App\Entity\FicheSuppl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FicheSuppl|null find($id, $lockMode = null, $lockVersion = null)
 * @method FicheSuppl|null findOneBy(array $criteria, array $orderBy = null)
 * @method FicheSuppl[]    findAll()
 * @method FicheSuppl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheSupplRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheSuppl::class);
    }

    // /**
    //  * @return FicheSuppl[] Returns an array of FicheSuppl objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FicheSuppl
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
