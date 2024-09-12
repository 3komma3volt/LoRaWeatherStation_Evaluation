<?php

namespace App\Controller;

use DatePeriod;
use DateInterval;
use App\Service\UiService;
use App\Entity\WeatherData;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\WeatherDataRepository;
use Doctrine\ORM\Query\Expr\Math;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompareController extends AbstractController
{
    #[Route('/compare', name: 'app_compare', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        WeatherDataRepository $weatherData,
    ): Response {
        $compareStations = $request->get('stations');

        if ($compareStations == null || $request->getRealMethod() == 'get') {
            return $this->redirectToRoute('app_stations');
        }

        $stationData = [];
        $allStationData = [];
        if ($compareStations) {

            foreach ($compareStations as $station) {
                $stationData = $weatherData->getStationWeatherData($station);
                foreach ($stationData as $stationDataElement => $stationDataValue) {
                    if (!in_array($stationDataElement, UiService::getIsMeasurement()) && $stationDataElement != 'alias' && $stationDataElement != 'dev_id') {
                        unset($stationData[$stationDataElement]);
                    }
                }
                $allStationData[] = $stationData;
                foreach ($allStationData as $n => $v) foreach ($v as $vn => $vv) $transposedData[$vn][$n] = $vv;
            }
        }

        //  dd($transposedData);
        return $this->render('compare/index.html.twig', [
            'allStationData' => $allStationData,
            'transposedData' => $transposedData,
            'compareableData' => UiService::getIsCompareable(),
            'measurementNames' => UiService::getMeasurementNames(),
            'measurementUnits' => UiService::getMeasurementUnits(),
            'measurementIcons' => UiService::getMeasurementIcon(),
        ]);
    }

    #[Route('/compare/stations', name: 'app_compare_stations', methods: ['GET', 'POST'])]
    public function compare(
        Request $request,
        WeatherDataRepository $weatherDataRepository,
        ChartBuilderInterface $chartBuilder
    ): Response {

        $compareParameter =  $request->get('parameter');
        $compareStations = $request->get('stations');
        $compareStationNames = $request->get('stationNames');
        $compareTimespan = $request->get('timespan') ?? 8;

        $weatherData = array();
        $weatherTimeStamp = array();
        $timestamps = array();

       // $weatherTimeStampReadable = array();
       $allTimestampsReadables = array();
        $maxMeasurements  = 0;
        foreach ($compareStations as $station) {

            $tmpWeatherData = $weatherDataRepository->getWeatherData($station, $compareTimespan);
            if ($tmpWeatherData[$compareParameter] == null) {
                // Skip station
            } else {
                $weatherData[] = $tmpWeatherData[$compareParameter];
                if(count($tmpWeatherData[$compareParameter]) > $maxMeasurements) {
                    $maxMeasurements = count($tmpWeatherData[$compareParameter]);
                }
                $weatherTimeStamp[] = $tmpWeatherData['datetime'];
                $allTimestampsReadables[] = $tmpWeatherData['datetime_readable'];
                $weatherTimeStampReadable = $tmpWeatherData['datetime_readable'];

                $timestamps = array_merge($timestamps, $weatherTimeStampReadable);
            }
            
        }     
        $uniqueTimestamps = array_unique($timestamps);
        sort($uniqueTimestamps);

        $minTimestamp = min(array_merge(...$weatherTimeStamp));
        $maxTimestamp = max(array_merge(...$weatherTimeStamp));
        $interval = new DateInterval('PT'.$this->getParameter('system_transmission_time').'M');
        $period = new DatePeriod($minTimestamp, $interval, $maxTimestamp);

    

       // dd($weatherData);
        $newData = array();

        foreach($weatherData as $index=>$value) {
       
        $cnt=0;
        foreach($uniqueTimestamps as $ts) {
           // print($index);
            if(in_array($ts, $allTimestampsReadables[$index])) {

                $newData[$index][] = $value[$cnt];
                $cnt++;
            }
            else {
                $newData[$index][] = -10;
            }
          
        }
    }

        //foreach ($weatherData as $index =>$item) {
        //    foreach($item as $wd) {
        //        if($weatherTimeStampReadable[$index] == $uniqueTimestamps[$index]) {
        //        $newData[$index][] = $wd; 
        //        }
//
        //    }

       // }
      //  dd($newData);
        $datasets = array();

        foreach ($compareStationNames as $index => $stationName) {
            $datasets[] = [
                'label' => $stationName,
                'data' => $weatherData[$index],
                'borderWidth' => 1
            ];
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $weatherTimeStampReadable,
            'datasets' => $datasets,
        ]);
        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => UiService::getMeasurementNames()[$compareParameter]  . "/" . UiService::getMeasurementUnits()[$compareParameter],
                    ],
                    'beginAtZero' => false,
                ],
            ],
        ]);

        return $this->render('compare/compare.html.twig', [
            'chart' => $chart,
            'stations' => $compareStationNames,
            'parameter' => $compareParameter,
            'station_ids' => $compareStations,
            'parameter_name' => UiService::getMeasurementNames()[$compareParameter],
            'timespan' => $compareTimespan
        ]);
    }
}
