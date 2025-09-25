<?php

namespace App\Twig;

use Twig\TwigFunction;

use Twig\Extension\AbstractExtension;
use Symfony\Contracts\Cache\CacheInterface;
use App\Repository\WeatherStationsRepository;
use Symfony\Contracts\Cache\ItemInterface;

class StationsExtension extends AbstractExtension
{
    private $weatherStationsRepository;
    private $cache;

    public function __construct(WeatherStationsRepository $ws, CacheInterface $cache)
    {
        $this->weatherStationsRepository = $ws;
        $this->cache = $cache;
    }

    public function getFunctions(): array 
    {
        return [
            new TwigFunction('getStationNames', [$this, 'getstationNames']),
        ];
    }

    public function getstationNames()
    {
        return $this->cache->get('station_names', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return $this->weatherStationsRepository->getStations(true, true);
        });
    }
}