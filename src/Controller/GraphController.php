<?php

namespace App\Controller;

use App\Service\UiService\UiService;
use App\Repository\WeatherDataRepository;
use Symfony\Component\Filesystem\Filesystem;
use App\Repository\WeatherStationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GraphController extends AbstractController
{
    #[Route('/graph/{id}/{timespan}', name: 'app_graph', defaults: ['timespan' => 8])]
    public function index(
        $id, $timespan,
        WeatherDataRepository $weatherData,
        WeatherStationsRepository $stations,
        ): Response
    {
        $ui = new UiService();
        $stationDetails = $stations->getStationsDetails(false, $id);
        $weather = $weatherData->getWeatherData($id, $timespan);


        $graphData = $ui->graphUiPrepare($weather);

        //dd($weather);
        return $this->render('graph/index.html.twig', [
            'stationData' => $stationDetails,
            'weatherHistoryData' => $weather,
            'graphData' => $graphData,
            'isMeasurement' => UiService::getIsMeasurement(),
            'measurementNames' => UiService::getMeasurementNames(),
            'measurementUnits' => UiService::getMeasurementUnits(),
            'detailedAttributes' => UiService::getIsDetailledAttribute(),
            'timespan' => $timespan
        ]);
    }

    #[Route('/graph/{id}/{timespan}/export', name: 'app_graph_export', defaults: ['timespan' => 8])]
    public function export(
        $id, $timespan,
        WeatherDataRepository $weatherData,
        ): BinaryFileResponse
    {

        $weather = $weatherData->getWeatherData($id, $timespan, true);

        $filename = "data_export_" .$id . "_" . date("d-m-Y") . ".csv";

        $tmpFileName = (new Filesystem())->tempnam(sys_get_temp_dir(), 'sb_');
        $tmpFile = fopen($tmpFileName, 'wb+');
        if (!\is_resource($tmpFile)) {
            throw new \RuntimeException('Unable to create a temporary file.');
        }
        foreach ($weather as $line) {
            if (is_a($line[0], 'DateTime')) continue;
            if ($line[0] == null) continue;
          fputcsv($tmpFile, $line, ",");
        }
        fclose($tmpFile);
        $response = $this->file($tmpFileName, $filename);
        $response->headers->set('Content-type', 'application/csv');

       

        return $response;

    }
}
