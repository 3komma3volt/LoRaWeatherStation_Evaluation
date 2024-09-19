<?php

namespace App\Controller;


use App\Service\UiService;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\WeatherDataRepository;
use Symfony\Component\Filesystem\Filesystem;
use App\Repository\WeatherStationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class GraphController extends AbstractController
{
    #[Route('/graph/{id}/{timespan}', name: 'app_graph', defaults: ['timespan' => 8])]
    public function index(
        $id,
        $timespan,
        WeatherDataRepository $weatherData,
        WeatherStationsRepository $stations,
        ChartBuilderInterface $chartBuilder,
        UiService $uiservice
    ): Response {

        $stationDetails = $stations->getStationsDetails(false, $id);
        $weather = $weatherData->getWeatherData($id, $timespan);

        $datetimeArray = $weather['datetime_readable'];

        $dataCharts = array();
        $detailedCharts = array();

        foreach ($weather as $key => $value) {
            if ($uiservice->containsOnlyNull($value)) {
                continue;
            }
            if(!in_array($key, UiService::getIsMeasurement()) && !in_array($key, UiService::getIsDetailledAttribute())) {
                continue;
            }
            $measurementChart = $chartBuilder->createChart(Chart::TYPE_LINE);
            $measurementChart->setData([
                'labels' => $datetimeArray,
                'datasets' => [
                    [
                        'label' => UiService::getMeasurementNames()[$key],
                        'data' => $value,
                        'borderWidth' => 1
                    ],
                ],
            ]);
            $measurementChart->setOptions([
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                ],

                'scales' => [
                    'y' => [
                        'title' => [
                            'display' => true,
                            'text' => UiService::getMeasurementNames()[$key] . "/" . UiService::getMeasurementUnits()[$key], // Dynamically set y-axis label using $params
                        ],
                        'beginAtZero' => false,
                    ],
                ],
            ]);
            if (in_array($key, UiService::IS_MEASUREMENT)) {
               
                $dataCharts[UiService::getMeasurementNames()[$key]] = $measurementChart;
            }
            else if (in_array($key, UiService::IS_DETAILLED_ATTRIBUTE)) {
            
                $detailedCharts[UiService::getMeasurementNames()[$key]] = $measurementChart;
            }
        }

        return $this->render('graph/index.html.twig', [
            'stationData' => $stationDetails,
            'timespan' => $timespan,
            'dataCharts' => $dataCharts,
            'detailedCharts' => $detailedCharts
        ]);
    }

    #[Route('/graph/{id}/{timespan}/export', name: 'app_graph_export', defaults: ['timespan' => 8])]
    public function export(
        $id,
        $timespan,
        WeatherDataRepository $weatherData,
    ): BinaryFileResponse {

        $weather = $weatherData->getWeatherData($id, $timespan);

        $filename = "data_export_" . $id . "_" . date("d-m-Y") . ".csv";

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
