<?php

namespace App\Twig;

use Twig\TwigFunction;

use Twig\Extension\AbstractExtension;
use Symfony\Contracts\Cache\CacheInterface;
use App\Repository\WeatherStationsRepository;

class StationsExtension extends AbstractExtension
{
    private $stationsNames;
    private $cache;

    public function __construct(WeatherStationsRepository $ws, CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->stationsNames = $this->cache->get('station_names', function () use ($ws) {
            return $ws->getStations(true, true);
        });
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getStationNames', [$this, 'getstationNames']),
        ];
    }

    public function getstationNames()
    {
        return $this->stationsNames;
    }
}