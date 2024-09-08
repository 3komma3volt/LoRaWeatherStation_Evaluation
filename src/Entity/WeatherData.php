<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Entity\WeatherStations;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\WeatherDataRepository;

#[ORM\Entity(repositoryClass: WeatherDataRepository::class)]
class WeatherData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $app_id = null;

    #[ORM\Column(length: 22)]
    private ?string $dev_id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ttn_time = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gtw_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $gtw_rssi = null;

    #[ORM\Column(nullable: true)]
    private ?int $gtw_snr = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_temperature = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_humidity = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_pressure = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_battery = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_wind = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_winddir = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_brightness = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_radiation = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_rain = null;

    #[ORM\Column(nullable: true)]
    private ?float $data_uv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getAppId(): ?string
    {
        return $this->app_id;
    }

    public function setAppId(?string $app_id): static
    {
        $this->app_id = $app_id;

        return $this;
    }

    public function getDevId(): ?string
    {
        return $this->dev_id;
    }

    public function setDevId(string $dev_id): static
    {
        $this->dev_id = $dev_id;

        return $this;
    }

    public function getTtnTime(): ?string
    {
        return $this->ttn_time;
    }

    public function setTtnTime(?string $ttn_time): static
    {
        $this->ttn_time = $ttn_time;

        return $this;
    }

    public function getGtwId(): ?string
    {
        return $this->gtw_id;
    }

    public function setGtwId(?string $gtw_id): static
    {
        $this->gtw_id = $gtw_id;

        return $this;
    }

    public function getGtwRssi(): ?int
    {
        return $this->gtw_rssi;
    }

    public function setGtwRssi(?int $gtw_rssi): static
    {
        $this->gtw_rssi = $gtw_rssi;

        return $this;
    }

    public function getGtwSnr(): ?int
    {
        return $this->gtw_snr;
    }

    public function setGtwSnr(?int $gtw_snr): static
    {
        $this->gtw_snr = $gtw_snr;

        return $this;
    }

    public function getDataTemperature(): ?float
    {
        return $this->data_temperature;
    }

    public function setDataTemperature(?float $data_temperature): static
    {
        $this->data_temperature = $data_temperature;

        return $this;
    }

    public function getDataHumidity(): ?float
    {
        return $this->data_humidity;
    }

    public function setDataHumidity(?float $data_humidity): static
    {
        $this->data_humidity = $data_humidity;

        return $this;
    }

    public function getDataPressure(): ?float
    {
        return $this->data_pressure;
    }

    public function setDataPressure(?float $data_pressure): static
    {
        $this->data_pressure = $data_pressure;

        return $this;
    }

    public function getDataBattery(): ?float
    {
        return $this->data_battery;
    }

    public function setDataBattery(?float $data_battery): static
    {
        $this->data_battery = $data_battery;

        return $this;
    }

    public function getDataWind(): ?float
    {
        return $this->data_wind;
    }

    public function setDataWind(?float $data_wind): static
    {
        $this->data_wind = $data_wind;

        return $this;
    }

    public function getDataWinddir(): ?float
    {
        return $this->data_winddir;
    }

    public function setDataWinddir(?float $data_winddir): static
    {
        $this->data_winddir = $data_winddir;

        return $this;
    }

    public function getDataBrightness(): ?float
    {
        return $this->data_brightness;
    }

    public function setDataBrightness(?float $data_brightness): static
    {
        $this->data_brightness = $data_brightness;

        return $this;
    }

    public function getDataRadiation(): ?float
    {
        return $this->data_radiation;
    }

    public function setDataRadiation(?float $data_radiation): static
    {
        $this->data_radiation = $data_radiation;

        return $this;
    }

    public function getDataRain(): ?float
    {
        return $this->data_rain;
    }

    public function setDataRain(?float $data_rain): static
    {
        $this->data_rain = $data_rain;

        return $this;
    }

    public function getDataUv(): ?float
    {
        return $this->data_uv;
    }

    public function setDataUv(?float $data_uv): static
    {
        $this->data_uv = $data_uv;

        return $this;
    }
}
