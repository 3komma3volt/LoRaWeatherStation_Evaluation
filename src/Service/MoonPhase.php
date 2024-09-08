<?php

namespace App\Service;

use DateTime;

class MoonPhase {
    private const PHASES = [
        "Full", "Waning Gibbous", "Third Quarter", "Waning Crescent",
        "New", "Waxing Crescent", "First Quarter", "Waxing Gibbous"
    ];
    private const PHASE_ICONS = [
        "wi-moon-full", "wi-moon-waning-gibbous-1", "wi-moon-third-quarter",
        "wi-moon-waning-crescent-1", "wi-moon-new", "wi-moon-waxing-crescent-1", "wi-moon-first-quarter", "wi-moon-waxing-gibbous-1"
    ];
    private const SYNODIC_MONTH = 29.530588853;
    private const BASE_JULIAN_DATE = 2451550.1;

    private int $day;
    private int $month;
    private int $year;
   

    public function __construct()
    {
        $currentDateTime = new DateTime('now');
        $this->day = (int)$currentDateTime->format('j');
        $this->month = (int)$currentDateTime->format('n');
        $this->year = (int)$currentDateTime->format('o');
    }
    /**
    * Calculates the current moon phase based on a given date or current date if no data is provided
    *
    *
    * @param int|null $day     The day.
    * @param int|null $month   The month.
    * @param int|null $year    The year.
    *
    * @return array            An array containing details about the moon phase.
    */
    public function calcMoonPhase($day = null, $month = null, $year = null): array {

        $day = $day ?? $this->day;
        $month = $month ?? $this->month;
        $year = $year ?? $this->year;

        if ($month <= 2) {
            $month += 12;
            $year -= 1;
        }

        $A = intval($year / 100);
        $B = 2 - $A + intval($A / 4);
        $julianDay = intval(365.25 * ($year + 4716)) + intval(30.6001 * ($month + 1)) + $day + $B - 1524.5;

        $daysSinceNew = $julianDay - self::BASE_JULIAN_DATE;
        $newMoons = $daysSinceNew / self::SYNODIC_MONTH;
        $moonAge = ($newMoons - intval($newMoons)) * self::SYNODIC_MONTH;
        $moonAge = round($moonAge, 2); 

        $phaseIndex = match (true) {
        $moonAge < 1 => 0,  // New Moon
        $moonAge < 8 => 1,  // Waxing Crescent
        $moonAge < 9 => 2,  // First Quarter
        $moonAge < 15 => 3, // Waxing Gibbous
        $moonAge < 16 => 4, // Full Moon
        $moonAge < 22 => 5, // Waning Gibbous
        $moonAge < 23 => 6, // Last Quarter
        default => 7,       // Waning Crescent
        };

        return [
            'phase' => self::PHASES[$phaseIndex],
            'moonage' => $moonAge,
            'phase_icon' => self::PHASE_ICONS[$phaseIndex]
        ];
    }
    
}