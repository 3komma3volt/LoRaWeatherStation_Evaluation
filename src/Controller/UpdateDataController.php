<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\WeatherData;
use App\Entity\WeatherStations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UpdateDataController extends AbstractController
{
  private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; 
    }

    
    #[Route('/updatedata', name: 'app_update_data', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $data = json_decode($request->getContent());

        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
          return new Response("Wrong data format", 400);
      }

        $station_temperature = $data->uplink_message->decoded_payload->temperature ?? null;
        $station_humidity = $data->uplink_message->decoded_payload->humidity;
        $station_pressure = $data->uplink_message->decoded_payload->pressure ?? null;
        $station_battery = $data->uplink_message->decoded_payload->battery ?? null;
        $station_rain = $data->uplink_message->decoded_payload->rain ?? null;
        $station_brightness = $data->uplink_message->decoded_payload->brightness ?? null;
        $station_wind = $data->uplink_message->decoded_payload->wind ?? null;
        $station_winddir = $data->uplink_message->decoded_payload->winddir ?? null;
        $station_radiation = $data->uplink_message->decoded_payload->radiation ?? null;
        $station_uv = $data->uplink_message->decoded_payload->uv ?? null;

        if(isset($station_brightness) && intval($station_brightness) == 0xFFFF ) {
            $station_brightness = null;
        }
        if(isset($station_rain) && intval($station_rain == 0xFF)) {
          $station_rain = null;
        }
       
//        $station_raw_payload = $data->uplink_message->frm_payload ?? null;  // No neccessary to store this information.
        $gtw_id = $data->uplink_message->rx_metadata[0]->gateway_ids->gateway_id ?? null; 
        $gtw_rssi = $data->uplink_message->rx_metadata[0]->rssi ?? null;
        $gtw_snr = $data->uplink_message->rx_metadata[0]->snr ?? null;

        $ttn_app_id = $data->end_device_ids->application_ids->application_id ?? null; 
        $ttn_dev_id = $data->end_device_ids->device_id ?? null;
        $ttn_time = $data->received_at ?? null; 

        $weatherStationRepository = $this->entityManager->getRepository(WeatherStations::class);

        $weatherStation = $weatherStationRepository->findOneBy(['dev_id' => $ttn_dev_id]);

        if (!$weatherStation) {
            $weatherStation = new WeatherStations();
            $weatherStation->setDevId($ttn_dev_id);
            $weatherStation->setAlias($ttn_dev_id);
        }

        $weatherStation->setLastUpdate(new DateTime("now", new DateTimeZone('Europe/Amsterdam')));
     
        $this->entityManager->persist($weatherStation); 
        $this->entityManager->flush();

        $weatherData = new WeatherData;
        $weatherData->setDatetime(new DateTime("now", new DateTimeZone('Europe/Amsterdam')));
        $weatherData->setAppId($ttn_app_id);
        $weatherData->setDevId($ttn_dev_id);
        $weatherData->setTtnTime($ttn_time);
        $weatherData->setGtwId($gtw_id);
        $weatherData->setGtwRssi($gtw_rssi);
        $weatherData->setGtwSnr($gtw_snr);
        $weatherData->setDataTemperature($station_temperature);
        $weatherData->setDataHumidity($station_humidity);
        $weatherData->setDataPressure($station_pressure);
        $weatherData->setDataBattery($station_battery);
        $weatherData->setDataWind($station_wind);
        $weatherData->setDataWinddir($station_winddir);
        $weatherData->setDataBrightness($station_brightness);
        $weatherData->setDataRadiation($station_radiation);
        $weatherData->setDataRain($station_rain);
        $weatherData->setDataUv($station_uv);

        $this->entityManager->persist($weatherData); 
        $this->entityManager->flush();

      return new Response("Data successfully added", 200);
    }
}
