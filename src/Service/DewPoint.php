<?php

namespace App\Service\DewPoint;

class DewPoint {
/********************************************************************
 * Ported from:
 *     File dew.js
 *     Copyright 2003 Wolfgang Kuehn
 *
 *     Licensed under the Apache License, Version 2.0 (the "License");
 *     you may not use this file except in compliance with the License.
 *     You may obtain a copy of the License at
 *           http://www.apache.org/licenses/LICENSE-2.0
 ********************************************************************/
// Changes BP: Converted to class
/*
 * Saturation Vapor Pressure formula for range -100..0 Deg. C.
 * This is taken from
 *   ITS-90 Formulations for Vapor Pressure, Frostpoint Temperature,
 *   Dewpoint Temperature, and Enhancement Factors in the Range 100 to +100 C
 * by Bob Hardy
 * as published in "The Proceedings of the Third International Symposium on Humidity & Moisture",
 * Teddington, London, England, April 1998
*/

    private const C_OFFSET = 273.15;
    private const MIN_T = 173; 
    private const MAX_T = 678;

    private const K0 = -5.8666426e3;
    private const K1 = 2.232870244e1;
    private const K2 = 1.39387003e-2;
    private const K3 = -3.4262402e-5;
    private const K4 = 2.7040955e-8;
    private const K5 = 6.7063522e-1;

/**
 * Saturation Vapor Pressure formula for range 273..678 Deg. K.
 * This is taken from the
 *   Release on the IAPWS Industrial Formulation 1997
 *   for the Thermodynamic Properties of Water and Steam
 * by IAPWS (International Association for the Properties of Water and Steam),
 * Erlangen, Germany, September 1997.
 *
 * This is Equation (30) in Section 8.1 "The Saturation-Pressure Equation (Basic Equation)"
 */


    private const N1 = 0.11670521452767e4;
    private const N2 = -0.72421316703206e6;
    private const N3 = -0.17073846940092e2;
    private const N4 = 0.12020824702470e5;
    private const N5 = -0.32325550322333e7;
    private const N6 = 0.14915108613530e2;
    private const N7 = -0.48232657361591e4;
    private const N8 = 0.40511340542057e6;
    private const N9 = -0.23855557567849;
    private const N10 = 0.65017534844798e3;

private function pvsIce($T)
{
  $lnP = self::K0 / $T + self::K1 + (self::K2 + (self::K3 + (self::K4 * $T)) * $T) * $T + self::K5 * log($T);
  return exp($lnP);
}

private function pvsWater(float $T): float
{
    $th = $T + self::N9 / ($T - self::N10);
    $A = ($th + self::N1) * $th + self::N2;
    $B = (self::N3 * $th + self::N4) * $th + self::N5;
    $C = (self::N6 * $th + self::N7) * $th + self::N8;

    $p = 2 * $C / (-$B + sqrt($B * $B - 4 * $A * $C));
    $p *= $p;
    $p *= $p;

    return $p * 1e6;
}

/**
 * Compute Saturation Vapor Pressure for minT<T[Deg.K]<maxT.
 */
private function PVS(float $T): ?float
{
    if ($T < self::MIN_T || $T > self::MAX_T) {
        return null;
    } elseif ($T < self::C_OFFSET) {
        return $this->pvsIce($T);
    } else {
        return $this->pvsWater($T);
    }
}

/**
 * Compute dewPoint for given relative humidity RH[%] and temperature T[Deg.C].
 */
public function dewPoint(float $RH, float $T): ?float
{
    if (!is_numeric($T)) {
        return null;
    }

    $T = $T + self::C_OFFSET;  // Convert Celsius to Kelvin
    $pvs = $this->PVS($T);

    if ($pvs === null) {
        return null;
    }

    return $this->solve($RH / 100 * $pvs, $T) - self::C_OFFSET;
}


/**
 * Newton's Method to solve f(x)=y for x with an initial guess of x0.
 */
private function solve(float $y, float $x0): float
{
    $x = $x0;
    $maxCount = 10;
    $count = 0;

    do {
        $dx = $x / 1000;
        $z = $this->PVS($x);
        $xNew = $x + $dx * ($y - $z) / ($this->PVS($x + $dx) - $z);

        if (abs(($xNew - $x) / $xNew) < 0.0001) {
            return $xNew;
        } elseif ($count > $maxCount) {
            return self::C_OFFSET; // Return default value if solution not found
        }

        $x = $xNew;
        $count++;
    } while (true);
}

}