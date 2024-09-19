<?php

namespace App\Repository;

use App\Entity\WeatherData;
use App\Entity\WeatherStations;
use App\Service\DewPoint;
use App\Service\UiService;
use App\Service\WeatherCalculations;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Functions\AvgFunction;

/**
 * @extends ServiceEntityRepository<WeatherData>
 */
class WeatherDataRepository extends ServiceEntityRepository
{
    private $entityManager;
    private const MEASUREMENT_DATA = [
        'data_temperature',
        'data_humidity',
        'data_pressure',
        'data_wind',
        'data_winddir',
        'data_brightness',
        'data_radiation',
        'data_rain',
        'data_uv',
    ];


    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager,

    ) {
        parent::__construct($registry, WeatherData::class);
        $this->entityManager = $entityManager;
    }


    public function getDashboardData()
    {
        $avgMeasurements = [];
        $avgCnt = [];

        foreach (self::MEASUREMENT_DATA as $data) {
            $avgMeasurements['avg_' . $data] = null;
        }

        foreach (self::MEASUREMENT_DATA as $data) {
            $avgCnt['cnt_' . $data] = 0;
        }


        $qb = $this->entityManager->createQueryBuilder();

        $subQuery = $this->entityManager->createQueryBuilder()
            ->select('MAX(wd_sub.id)')
            ->from(WeatherData::class, 'wd_sub')
            ->where('wd_sub.dev_id = wd.dev_id')
            ->getDQL();

        $qb->select('wd')
            ->from(WeatherData::class, 'wd')
            ->where($qb->expr()->in('wd.id', $subQuery))
            ->andWhere("JSON_EXTRACT(ws.status, '$.dashboardignore') != 'true'")
            ->andWhere('ws.last_update >= :thirtyMinutesAgo')
            ->setParameter('thirtyMinutesAgo', new \DateTime('-30 minutes'))
            ->join(WeatherStations::class, 'ws', Join::WITH, 'wd.dev_id = ws.dev_id');

        $result = ($qb->getQuery()->getArrayResult());


        foreach ($result as $res) {
            foreach ($res as $key => $value) {
                if ($value == null) continue;
                if (in_array($key, self::MEASUREMENT_DATA)) {
                    $avgMeasurements['avg_' . $key] += $value;
                    $avgCnt['cnt_' . $key] += 1;
                }
            }
        }
        foreach (self::MEASUREMENT_DATA as $key) {
            if ($avgMeasurements['avg_' . $key]  == null) continue;
            $avgMeasurements['avg_' . $key] /=  $avgCnt['cnt_' . $key];
        }

        return [
            'avgMeasurements' => $avgMeasurements,
            'stationCount' => count($result)
        ];
    }


    public function getStationWeatherData(string $devId)
    {
        // todo: change entities and add many-to-one relation
        $qb = $this->createQueryBuilder('wd')
        ->select(
            'wd.id, wd.datetime, wd.app_id, wd.dev_id, wd.ttn_time, wd.gtw_id, wd.gtw_rssi, wd.gtw_snr, wd.data_temperature, wd.data_humidity, wd.data_pressure, wd.data_battery, wd.data_wind, wd.data_winddir, wd.data_brightness, wd.data_radiation, wd.data_rain, wd.data_uv',
            'ws.id AS station_id, ws.dev_id AS station_dev_id, ws.alias, ws.latitude, ws.longitude, ws.altitude, ws.last_update'
        ) ->innerJoin('App\Entity\WeatherStations', 'ws', 'WITH', 'ws.dev_id = wd.dev_id')
        ->where('wd.dev_id = :deviceId')
        ->setParameter('deviceId', $devId)
        ->orderBy('wd.datetime', 'DESC')
        ->setMaxResults(1);
        $result = $qb->getQuery()->getArrayResult()[0];

        if(!$result) {
            return null;
        }

        if ($result['data_temperature'] && $result['data_humidity']) {
            $dp = new DewPoint();

            $dewPoint = $dp->dewpoint($result['data_humidity'], $result['data_temperature']);
            $result['data_dewpoint'] = $dewPoint;
        }
        if ($result['data_battery']) {
            $ui = new UiService();
            $result['data_battery_level'] = $ui->calculateBatteryLevel($result['data_battery']);
        }

        if ($result['data_rain']) {
            $rainOneHour = array_sum($this->getStationRain($devId));
            $result['data_rain'] = $rainOneHour;
        }

        if($result['data_temperature'] && $result['data_pressure'] && $result['altitude']) {
            $calculations = new WeatherCalculations();
            $result['data_pressure_sealevel'] = $calculations->getPressureAtSealevel($result['data_pressure'], $result['data_temperature'], $result['altitude']);
        }

        return $result;
    }

    private function getLastTime($devId)
    {
        $latestEntry = $this->createQueryBuilder('wd')
            ->where('wd.dev_id = :deviceId')
            ->setParameter('deviceId', $devId)
            ->orderBy('wd.datetime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$latestEntry) {
            return null;
        }
        return $latestEntry->getDatetime();
    }

    public function getStationRain(string $devId, $timeSpan = 1)
    {

        $latestEntry = $this->getLastTime($devId);

        $startTime = (clone $latestEntry)->modify("-{$timeSpan} hour");

        $qb = $this->createQueryBuilder('wd')
            ->select('wd.data_rain')
            ->where('wd.dev_id = :deviceId')
            ->andWhere('wd.datetime BETWEEN :oneHourBefore AND :lastEntryTime')
            ->setParameter('deviceId', $devId)
            ->setParameter('oneHourBefore', $startTime)
            ->setParameter('lastEntryTime', $latestEntry)
            ->orderBy('wd.datetime', 'DESC');

        $results = $qb->getQuery()->getResult();

        return array_column($results, 'data_rain');
    }

    public function getWeatherData(string $devId, int $timeSpan): array
    {
        $latestEntry = $this->getLastTime($devId);
        $startTime = (clone $latestEntry)->modify("-{$timeSpan} hour");
        $qb = $this->createQueryBuilder('wd')
            ->where('wd.dev_id = :deviceId')
            ->andWhere('wd.datetime BETWEEN :oneHourBefore AND :lastEntryTime')
            ->setParameter('deviceId', $devId)
            ->setParameter('oneHourBefore', $startTime)
            ->setParameter('lastEntryTime', $latestEntry)
            ->setParameter('deviceId', $devId)
            ->orderBy('wd.datetime', 'ASC');

        $resultArray = $qb->getQuery()->getArrayResult();

        $data_array = array();
        foreach ($resultArray as $ra) {
            $data_array['datetime'][] = $ra['datetime'];
            $data_array['datetime_readable'][] = $ra['datetime']->format('d.m H:i');        

            $data_array['data_temperature'][] = $ra['data_temperature'];
            $data_array['data_pressure'][] = $ra['data_pressure'];
            $data_array['data_battery'][] = $ra['data_battery'];
            $data_array['data_wind'][] = $ra['data_wind'];
            $data_array['data_radiation'][] = $ra['data_radiation'] ?? null;
            $data_array['data_brightness'][] = $ra['data_brightness'];
            $data_array['data_humidity'][] = $ra['data_humidity'];
            $data_array['data_rain'][] = $ra['data_rain'];
        }

        return $data_array;
    }
}
