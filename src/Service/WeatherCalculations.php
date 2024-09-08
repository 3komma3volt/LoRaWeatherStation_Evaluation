<?php

namespace App\Service;


class WeatherCalculations {
/**
 * Calculates the atmospheric pressure at sea level
 *
 *
 * @param float $pressure      The measured atmospheric pressure at the given altitude in hPa
 * @param float $temperature   The temperature in C.
 * @param float $altitude      The altitude above sea level in m.
 *
 * @return float               The atmospheric pressure at sea level in hPa
 */
    public static function getPressureAtSealevel($pressure, $temperature, $altitude) {
        return $pressure * pow(1 - 0.0065 * $altitude / ($temperature + 0.0065 * $altitude + 273.15), -5.275);
    } 
}