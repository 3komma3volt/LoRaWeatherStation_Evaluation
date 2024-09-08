<?php

namespace App\Controller;

use App\Service\UiService\UiService;
use App\Repository\WeatherDataRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompareController extends AbstractController
{
    #[Route('/compare', name: 'app_compare', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        WeatherDataRepository $weatherData,
    ): Response {
        $compareStations = $request->get('stations');


        $stationData = [];
        $allStationData = [];
        if($compareStations) {

               foreach ($compareStations as $station) {
            $stationData = $weatherData->getStationWeatherData($station);
            foreach ($stationData as $stationDataElement => $stationDataValue) {
                if (!in_array($stationDataElement, UiService::getIsMeasurement()) && $stationDataElement != "alias" && $stationDataElement != "dev_id") {
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
        WeatherDataRepository $weatherData,
    ): Response {
    }
}
