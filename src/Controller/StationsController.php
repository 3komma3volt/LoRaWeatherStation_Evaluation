<?php

namespace App\Controller;

use App\Service\UiService;
use App\Repository\WeatherDataRepository;
use App\Repository\WeatherStationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StationsController extends AbstractController
{
    #[Route('/stations', name: 'app_stations')]
    public function index(
        WeatherStationsRepository $weatherstations
    ): Response
    {
        $stations =  $weatherstations->getStationsDetails();

        return $this->render('stations/index.html.twig', ['weather_stations' => $stations]);
    }

    #[Route('/stations/{id}', name: 'app_station_detail')]
    public function stationDetail(
        $id,
        WeatherDataRepository $weatherData,
        WeatherStationsRepository $stations
    ): Response
    {
        $weatherData = $weatherData->getStationWeatherData($id);
        $stationDetails = $stations->getStationsDetails(false, $id);
        $stationStatus = $stations->getStationStatus($id);


//dd(UiService::getIsMeasurement());

        return $this->render('stations/station_details.html.twig', [
            'weatherData' => $weatherData,
            'stationData' => $stationDetails,
            'isMeasurement' => UiService::getIsMeasurement(),
            'measurementNames' => UiService::getMeasurementNames(),
            'measurementUnits' => UiService::getMeasurementUnits(),
            'measurementIcons' => UiService::getMeasurementIcon(),
            'measurementPrecission' => UiService::getMeasurementPrecission(),
            'sensorAttributes' => UiService::getIsSensorAttribute(),
            'detailedAttributes' => UiService::getIsDetailledAttribute(),
            'stationFlags' => $stationStatus,
            'stationFlagNames' => UiService::getStationFlagNames(),
            'stationFlagIcons' => UiService::getStationFlagIcons()
  
        ]);
    }

    #[Route('/stations/{id}/json', name: 'app_station_detail_json')]
    public function stationDetailJson(
        $id,
        WeatherDataRepository $weatherData,
        WeatherStationsRepository $stations
    ): JsonResponse
    {
        $weatherData = $weatherData->getStationWeatherData($id);
        $stationDetails = $stations->getStationsDetails(false, $id);
        
        $jsonData = array(
            ...$stationDetails,
            ...$weatherData,
          );
      
          $response = new JsonResponse($jsonData);
          $response->setEncodingOptions(JSON_PRETTY_PRINT);
          return $response;
    }
}
