<?php

namespace App\Controller;

use App\Entity\WeatherStations;
use App\Repository\WeatherDataRepository;
use DateTime;
use App\Service\MoonPhase;
use App\Repository\WeatherStationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{

  #[Route(path: '/', name: 'app_index')]
  #[Route(path: '/dashboard', name: 'app_dashboard')]
  public function indexAction(
    MoonPhase $moonPhase,
    WeatherDataRepository $weatherdata,
    WeatherStationsRepository $stations
  ): Response {

    $dashboardData = $this->getDashboardData($moonPhase, $weatherdata, $stations);

    return $this->render('dashboard/index.html.twig', [
      'moonData'  => $dashboardData['moonData'],
      'sunInfo' => $dashboardData['sunInfo'],
      'weatherData' => $dashboardData['weatherData']
    ]);
  }

  #[Route(path: '/dashboard/json', name: 'app_dashboard_json')]
  public function jsonAction(
    MoonPhase $moonPhase,
    WeatherDataRepository $weatherdata,
    WeatherStationsRepository $stations
  ): JsonResponse {

    $dashboardData = $this->getDashboardData($moonPhase, $weatherdata, $stations);

    $jsonData = array(
      'moon_phase' => $dashboardData['moonData']['phase'],
      'moon_age' => $dashboardData['moonData']['moonage'],
      'sunrise' => $dashboardData['sunInfo']['sunrise'],
      'sunset' => $dashboardData['sunInfo']['sunset'],
      'station_count' => $dashboardData['stations'],
      ...$dashboardData['weatherData'],
    );

    $response = new JsonResponse($jsonData);
    $response->setEncodingOptions(JSON_PRETTY_PRINT);
    return $response;
  }

  private function getDashboardData(
    MoonPhase $moonPhase,
    WeatherDataRepository $weatherdata
  ): array {
    $moonData = $moonPhase->calcMoonPhase();

    $timestamp = strtotime('today');
    $sunInfoRaw = date_sun_info($timestamp, $this->getParameter('system_latitude'), $this->getParameter('system_longitude'));

    $sunInfo = array_map(function ($val) {
      return date("H:i:s", $val);
    }, $sunInfoRaw);

    $weatherData = $weatherdata->getDashboardData();

    return [
      'moonData' => $moonData,
      'sunInfo' => $sunInfo,
      'stations' => $weatherData['stationCount'],
      'weatherData' => $weatherData['avgMeasurements'],
    ];
  }
}
