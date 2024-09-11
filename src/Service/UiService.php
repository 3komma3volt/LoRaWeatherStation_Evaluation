<?php

namespace App\Service\UiService;

class UiService
{
    public const MEASUREMENT_NAMES = [
        'data_temperature' => 'Temperature',
        'data_humidity' => 'Humidity',
        'data_pressure' => 'Pressure',
        'data_battery' => 'Battery',
        'data_battery_level' => 'Battery',
        'data_wind' => 'Wind',
        'data_brightness' => 'Brightness',
        'data_radiation' => 'Radiation',
        'dev_id' => 'Device ID',
        'datetime' => 'Reading Time',
        'gtw_id' => 'Gateway ID',
        'gtw_snr' => 'SNR',
        'gtw_rssi' => "RSSI",
        'data_dewpoint' => "Dew Point",
        "data_rain" => "Rain",
        'data_pressure_sealevel' => 'Pressure at sealevel',
        'data_forecast_text' => 'Forecast',
    ];

    public const MEASUREMENT_UNITS = [
        'data_temperature' => '°C',
        'data_humidity' => '%',
        'data_pressure' => 'hPa',
        'data_battery' => 'V',
        'data_battery_level' => '%',
        'data_wind' => 'm/s',
        'data_brightness' => 'lx',
        'data_radiation' => 'uSv',
        'dev_id' => '',
        'gtw_id' => '',
        'datetime' => '',
        'gtw_snr' => 'dB',
        'gtw_rssi' => 'dBm',
        'data_dewpoint' => '°C',
        'data_rain' => 'ml/h',
        'data_pressure_sealevel' => 'hPa',
        'data_forecast_text' => '',
    ];

    public const MEASUREMENT_ICON = [
        'data_temperature' => 'wi wi-thermometer',
        'data_humidity' => 'wi wi-humidity',
        'data_pressure' => 'wi wi-barometer',
        'data_battery' => 'bi bi-battery-half',
        'data_battery_level' => 'bi bi-battery-half',
        'data_wind' => 'wi wi-strong-wind',
        'data_brightness' => 'wi wi-sunset',
        'data_radiation' => 'bi bi-radioactive',
        'dev_id' => 'bi bi-card-list',
        'datetime' => 'bi bi-clock',
        'gtw_id' => 'bi bi-router',
        'gtw_snr' => 'bi bi-soundwave',
        'gtw_rssi' => 'bi bi-wifi-2',
        'data_dewpoint' => 'wi wi-raindrops',
        'data_rain' => 'bi bi-cloud-hail',
        'data_pressure_sealevel' => 'bi bi-align-bottom',
        'data_forecast_text' => 'bi bi-reception-3'
    ];
    public const MEASUREMENT_PRECISSION = [
        'data_temperature' => 1,
        'data_humidity' => 0,
        'data_pressure' => 0,
        'data_battery' => 2,
        'data_battery_level' => 0,
        'data_wind' => 2,
        'data_brightness' => 0,
        'data_radiation' => 2,
        'data_dewpoint' => 1,
        "data_rain" => 2,
        'data_pressure_sealevel' => 0,
    ];

    public const IS_MEASUREMENT = [
        'data_temperature',
        'data_humidity',
        'data_pressure',
        'data_pressure_sealevel',
        'data_wind',
        'data_brightness',
        'data_battery_level',
        'data_radiation',
        'data_dewpoint',
        'data_rain',
        'data_forecast_text'
    ];

    public const IS_SENSOR_ATTRIBUTE = [
        'datetime'
    ];

    public const IS_COMPAREABLE = [
        'data_temperature',
        'data_humidity',
        'data_pressure',
        'data_wind',
        'data_brightness'
    ];

    public const IS_DETAILLED_ATTRIBUTE = [
        'dev_id',
        'gtw_id',
        'gtw_snr',
        'gtw_rssi',
        'data_battery'
    ];

    public const STATION_FLAG_NAMES =  [
        'ok' => "OK",
        'beta' => "Beta",
        'maintenence' => "Maintenence",
        'invisible' => "Invisible",
        'dashboardignore' => "Ignore from Dashboard"
    ];

    public const STATION_FLAG_ICONS = [
        'ok' => 'bi bi-check',
        'beta' => 'bi bi-bug-fill',
        'maintenence' => 'bi bi-wrench',
        'invisible' => 'bi bi-eye-slash',
        'dashboardignore' => 'bi bi-exclude'
    ];

    /**
    * Calculates the battery level percentage based on the battery voltage.
    *
    * @param float|null $batteryVoltage   The current battery voltage in V.
    *
    * @return float|null                  The calculated battery level in %
    */
    public static function calculateBatteryLevel($batteryVoltage)
    {
        if ($batteryVoltage === null) {
            return null;
        }
        $maxBattery = 4.192;
        $minBattery = 3.5;
        $BAT_LEVEL_EMPTY = 0x01;
        $BAT_LEVEL_FULL = 0xFE;

        //$batlevel = max($minBattery, $BAT_LEVEL_EMPTY + (($battery - $minBattery) / ($maxBattery - $minBattery)) * ($BAT_LEVEL_FULL - $BAT_LEVEL_EMPTY));
        // $batterylevel  = round(102.13 *   pow($battery,2) - 633.65 * $battery  + 971.18);

        $high = 4.2;
        $low = 3.02;
        $batterylevel = (($batteryVoltage - $low) / ($high - $low)) * 100;

        //   $batterylevel = round($batlevel / 254 * 100);
        if ($batterylevel > 100) {
            $batterylevel = 100;
        }
        if ($batterylevel < 0) {
            $batterylevel = 0;
        }

        return round($batterylevel, 0);
    }

    /**
     * Checks if an array contains only null values     
     * 
     * @param array $input   The array to be checked.
     *
     * @return bool          True if the array contains only null values, false otherwise.
     */

    public static function containsOnlyNull($input)
    {
      return empty(array_filter($input, function ($a) {
        return $a !== null;
      }));
    }


    public static function getMeasurementPrecission()
    {
        return self::MEASUREMENT_PRECISSION;
    }

    public static function getStationFlagNames(): array
    {
        return self::STATION_FLAG_NAMES;
    }

    public static function getStationFlagIcons(): ?array
    {
        return self::STATION_FLAG_ICONS;
    }

    // Getter for MEASUREMENT_NAMES
    public static function getMeasurementNames(): array
    {
        return self::MEASUREMENT_NAMES;
    }

    // Getter for MEASUREMENT_VALUES
    public static function getMeasurementUnits(): array
    {
        return self::MEASUREMENT_UNITS;
    }

    // Getter for MEASUREMENT_ICON
    public static function getMeasurementIcon(): array
    {
        return self::MEASUREMENT_ICON;
    }

    // Getter for IS_MEASUREMENT
    public static function getIsMeasurement(): array
    {
        return self::IS_MEASUREMENT;
    }

    // Getter for IS_COMPAREABLE
    public static function getIsCompareable(): array
    {
        return self::IS_COMPAREABLE;
    }

    // Getter for IS_DETAILLED_ATTRIBUTE
    public static function getIsDetailledAttribute(): array
    {
        return self::IS_DETAILLED_ATTRIBUTE;
    }

    public static function getIsSensorAttribute(): array
    {
        return self::IS_SENSOR_ATTRIBUTE;
    }
}
