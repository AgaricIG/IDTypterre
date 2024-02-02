<?php

namespace App\Repository;

use App\Entity\Uts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Uts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Uts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Uts[]    findAll()
 * @method Uts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Uts::class);
    }

    public function index($page, $nbPerPage, $search = null)
    {
        $qb = $this->createQueryBuilder('u');

        if(!empty($search)) {
          $qb->andWhere('UPPER(u.name) LIKE UPPER(:search)')->setParameter('search', '%'.$search.'%');
        }

        $qb->getQuery();
        $qb->orderBy('u.name', 'ASC');
        $qb->setFirstResult(($page-1) * $nbPerPage)->setMaxResults($nbPerPage);

        return new Paginator($qb, true);
    }

    // /**
    //  * @return Uts[] Returns an array of Uts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Uts
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
