<?php

namespace App\Repository;

use App\Entity\Ucs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Ucs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ucs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ucs[]    findAll()
 * @method Ucs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UcsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ucs::class);
    }

    public function index($search = null)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u.id, u.name, u.tree, u.geom');

        if(!empty($search)) {
          $qb->andWhere('UPPER(u.name) LIKE UPPER(:search)')->setParameter('search', '%'.$search.'%');
        }

        $qb->orderBy('u.name','ASC');

        return $qb->getQuery()->getResult();
    }

    public function getUcsAtCoords(float $lat, float $lng, int $srid = null)
    {
        if(isset($srid)) {
            $sql = "SELECT id FROM ucs WHERE ST_Contains(ST_Transform(geom, $srid), ST_SetSRID(ST_Point($lng, $lat), $srid))";
        } else {
            $sql = "SELECT id FROM ucs WHERE ST_Contains(geom, St_SetSRID(ST_Point($lng, $lat), ST_SRID(geom)))";
        }

        $stmt = $this->_em->getConnection()->prepare($sql);
        $query = $stmt->executeQuery();
        $res = $query->fetch();
        if(isset($res['id'])) {
          return $this->find($res['id']);
        } else {
          return null;
        }
    }


    public function getGeojson(Ucs $ucs, $internalUse = true, $srid = 4326)
    {
        $sql = "SELECT jsonb_build_object('type', 'FeatureCollection', 'features', coalesce(jsonb_agg(feature)::json, '[]'::json))
                FROM (
                SELECT jsonb_build_object(
                          'type', 'Feature',
                          'geometry', ST_AsGeoJSON(ST_Transform(geom, ".$srid."))::jsonb,
                          'properties', jsonb_build_object(
                              'name', name,
                              ".((true === $internalUse) ? '\'id\', id,' : '').'
                              '.((true === $internalUse) ? '\'geom\', geom::text,' : '')."
                              'x', ST_X(ST_Transform(ST_Centroid(geom),4326))::numeric(10,7),
                              'y', ST_Y(ST_Transform(ST_Centroid(geom),4326))::numeric(10,7),
                              'xmin', ST_XMin(ST_Transform(geom,4326))::numeric(10,7),
                              'xmax', ST_XMax(ST_Transform(geom,4326))::numeric(10,7),
                              'ymin', ST_YMin(ST_Transform(geom,4326))::numeric(10,7),
                              'ymax', ST_YMax(ST_Transform(geom,4326))::numeric(10,7),
                              'surf', ST_Area(ST_Transform(geom,2154))::numeric(10,0)

                          )
                        ) AS feature
                FROM (SELECT * from ucs WHERE id = ".$ucs->getId().') inputs) features
            ';

        $stmt = $this->_em->getConnection()->prepare($sql);
        $query = $stmt->executeQuery();
        $result = $query->fetch();
        $geojson = $result['jsonb_build_object'];

        return $geojson;
    }
    // /**
    //  * @return Ucs[] Returns an array of Ucs objects
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
    public function findOneBySomeField($value): ?Ucs
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
