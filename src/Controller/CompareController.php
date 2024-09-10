<?php

namespace App\Controller;

use App\Service\UiService\UiService;
use App\Repository\WeatherDataRepository;
use App\Repository\WeatherStationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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
            'compareableData' => UiService::IS_COMPAREABLE,
            'measurementNames' => UiService::getMeasurementNames(),
            'measurementUnits' => UiService::getMeasurementUnits(),
            'measurementIcons' => UiService::getMeasurementIcon(),
        ]);
    }

    #[Route('/compare/stations', name: 'app_compare_stations', methods: ['GET', 'POST'])]
    public function compare(
        Request $request,
        WeatherDataRepository $weatherDataRepository,
        WeatherStationsRepository $weatherStationRepository,
        ChartBuilderInterface $chartBuilder
    ): Response {

        $compareParameter =  $request->get('parameter');
        $compareStations = $request->get('stations');
        $compareStationNames = $request->get('stationNames');
        $compareTimespan = $request->get('timespan') ?? 8;

        $weatherData = array();
        $weatherTimeStamp = array();

        $weatherTimeStampReadable = array();

        foreach ($compareStations as $station) {

            $tmpWeatherData = $weatherDataRepository->getWeatherData($station, $compareTimespan);
            if ($tmpWeatherData[$compareParameter] == null) {
                // Skip station
            } else {
                $weatherData[] = $tmpWeatherData[$compareParameter];
                $weatherTimeStamp[] = $tmpWeatherData['datetime'];
                $weatherTimeStampReadable = $tmpWeatherData['datetime_readable'];
            }
        }

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
