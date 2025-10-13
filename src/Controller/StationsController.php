<?php

namespace App\Controller;

use App\Service\UiService;
use App\Repository\WeatherDataRepository;
use App\Repository\WeatherStationsRepository;
use App\Form\StationSettingsType;
use App\Entity\WeatherStations;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/stations_update/{id}', name: 'app_station_update')]
    #[IsGranted('ROLE_ADMIN')]
    public function stationUpdate(
        $id,
        Request $request,
        WeatherDataRepository $weatherDataRepository,
        WeatherStationsRepository $stations,
        EntityManagerInterface $em
    ): Response
    {
        $station = $stations->findOneBy(['dev_id' => $id]);
        if (!$station) {
            throw $this->createNotFoundException('Station not found');
        }

        $form = $this->createForm(StationSettingsType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($station);
            $em->flush();
            $this->addFlash('success', 'Station data updated');
        } else {
            if ($form->isSubmitted()) {
                $this->addFlash('warning', 'Invalid form submission');
            }
        }

        return $this->redirectToRoute('app_station_detail', ['id' => $id]);
    }

    #[Route('/stations/{id}', name: 'app_station_detail')]
    public function stationDetail(
        $id,
        WeatherDataRepository $weatherDataRepository,
        WeatherStationsRepository $stations,
        Request $request
    ): Response
    {

        
         $station = $stations->findOneBy(['dev_id' => $id]);
         if (!$station) {
            throw $this->createNotFoundException('Station not found');
        }

        $form = $this->createForm(
            StationSettingsType::class,
           $station
        );

        $form->handleRequest($request);


        $weatherData = $weatherDataRepository->getStationWeatherData($id);

        if (!$weatherData) {
            $this->addFlash('warning', 'No weather data found for this station');
            return $this->redirectToRoute('app_stations');
        }

        $stationDetails = $stations->getStationsDetails($id, false);
        $stationStatus = $stations->getStationStatus($id);

        $pressureData = $weatherDataRepository->getStationPressure($id, 8);
      //  dd($pressureData);
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
            'stationFlagIcons' => UiService::getStationFlagIcons(),
            'form' => $form
  
        ]);
    }

    #[Route('/stations/{id}/json', name: 'app_station_detail_json')]
    public function stationDetailJson(
        $id,
        WeatherDataRepository $weatherDataRepository,
        WeatherStationsRepository $stations
    ): JsonResponse
    {
        $weatherData = $weatherDataRepository->getStationWeatherData($id);
        $stationDetails = $stations->getStationsDetails($id, false);
        
        $jsonData = array(
            ...$stationDetails,
            ...$weatherData,
          );
      
          $response = new JsonResponse($jsonData);
          $response->setEncodingOptions(JSON_PRETTY_PRINT);
          return $response;
    }
}
