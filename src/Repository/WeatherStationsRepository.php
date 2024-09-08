<?php

namespace App\Repository;


use App\Entity\WeatherStations;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Json;

/**
 * @extends ServiceEntityRepository<WeatherStations>
 */
class WeatherStationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, WeatherStations::class);
    }

    public function getStations(
        bool $ignore_invisible_stations = true,
        bool $name_alias_only = false
    ): array {
        $db = $this->createQueryBuilder('w');

        if ($ignore_invisible_stations) {
            $db->andWhere("JSON_EXTRACT(w.status, '$.invisible') != 'true'");
        }
        if ($name_alias_only) {
            $db->addSelect('w.dev_id, w.alias');
        }
  
        return $db->getQuery()->getResult();
    }

    public function stationCount($ignore_invisible_stations = true): int
    {
        $db = $this->createQueryBuilder('w')
            ->select('COUNT(DISTINCT w.dev_id)');
        if ($ignore_invisible_stations) {
            $db->where("JSON_EXTRACT(w.status, '$.invisible') != 'true'");
        }
        return $db->getQuery()->getSingleScalarResult();
    }

    public function getStationsDetails(
        bool $ignore_invisible_stations = true, string $dev_id = null

    ) {
        $db = $this->createQueryBuilder('w');

        if ($ignore_invisible_stations) {
            $db->andWhere("JSON_EXTRACT(w.status, '$.invisible') != 'true'");
        }

        if($dev_id) {
            $db->andWhere('w.dev_id = :dev_id');
            $db->setParameter('dev_id', $dev_id);
            $db->setMaxResults(1);
            $results = $db->getQuery()->getArrayResult();
            return $results[0];
        }
        else {
            $db->orderBy('w.alias', 'ASC');
            $results = $db->getQuery()->getResult();
            return $results;
        }
     
       return null;
    }

    public function getStationStatus(string $dev_id): ?array
    {
        $qb = $this->createQueryBuilder('w')
            ->select('w.status')
            ->where('w.dev_id = :dev_id')
            ->setParameter('dev_id', $dev_id)
            ->getQuery();

        $result = $qb->getOneOrNullResult();
        return $result['status'];
    }

}
