<?php

namespace App\DataFixtures;

use DateTime;
use DatePeriod;
use DateInterval;
use DateTimeZone;
use App\Entity\WeatherData;
use App\Entity\WeatherStations;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $weatherStation1 = new WeatherStations();
        $weatherStation1->setDevId('eui-70b3d57ed00538a3');
        $weatherStation1->setAlias('Station 1');
        $weatherStation1->setAltitude(500);
        $weatherStation1->setDescription('Station with all sensors');
        $weatherStation1->setStatus(['ok' => true, 'beta' => false, 'maintenence' => false, 'invisible' => false, 'dashboardignore' => false]);



        $weatherStation2 = new WeatherStations();
        $weatherStation2->setDevId('eui-ffffd57ed005ffff');
        $weatherStation2->setAlias('Station 2');
        $weatherStation2->setAltitude(400);
        $weatherStation2->setDescription('Station with some sensors');
        $weatherStation2->setStatus(['ok' => true, 'beta' => false, 'maintenence' => false, 'invisible' => false, 'dashboardignore' => false]);


        $weatherStation3 = new WeatherStations();
        $weatherStation3->setDevId('eui-ffffd57ed005aaaa');
        $weatherStation3->setAlias('Station 3 invisible');
        $weatherStation3->setStatus(['ok' => true, 'beta' => false, 'maintenence' => false, 'invisible' => true, 'dashboardignore' => false]);

        $weatherStation4 = new WeatherStations();
        $weatherStation4->setDevId('eui-ffffd57ed005bbbb');
        $weatherStation4->setAlias('Station 4 Beta');

        $weatherStation4->setStatus(['ok' => true, 'beta' => true, 'maintenence' => false, 'invisible' => false, 'dashboardignore' => false]);
        $manager->persist(($weatherStation4));

        $weatherStation5 = new WeatherStations();
        $weatherStation5->setDevId('eui-ffffd57ed-ignore');
        $weatherStation5->setAlias('Station 5 Dashboardignore');

        $weatherStation5->setStatus(['ok' => true, 'beta' => true, 'maintenence' => false, 'invisible' => false, 'dashboardignore' => true]);





        $date = new DateTime("now", new DateTimeZone('Europe/Amsterdam'));

        $startTime = clone $date;
        $startTime->modify('-500 minutes');
        $endTime = clone $date;
        $interval = new DateInterval('PT5M');
        $period = new DatePeriod($startTime, $interval, $endTime);

        foreach ($period as $dt) {
            echo $dt->format("d.m.Y H:i:s") . "\n";

            $weatherData = new WeatherData;
            $weatherData->setDevId('eui-70b3d57ed00538a3');
            $weatherData->setDatetime(clone $dt);
            $weatherData->setAppId('weatherdata-lora');
            $weatherData->setTtnTime($dt->format('Y-m-dTH:i:s') . 'Z');
            $weatherData->setGtwId('flowerstreet');
            $weatherData->setGtwRssi(rand(60, 90));
            $weatherData->setGtwSnr(rand(50, 100) / 10);
            $weatherData->setDataTemperature(rand(15, 25));
            $weatherData->setDataHumidity(rand(50, 100));
            $weatherData->setDataPressure(rand(800, 1100));
            $weatherData->setDataBattery(rand(35, 41) / 10);
            $weatherData->setDataWind(rand(0, 100) / 10);
            $weatherData->setDataWinddir(0);
            $weatherData->setDataBrightness(rand(0, 10000));
            $weatherData->setDataRadiation(0);
            $weatherData->setDataRain(rand(0, 5));
            $weatherData->setDataUv(0);
            $manager->persist(($weatherData));
            $weatherStation1->setLastUpdate(clone $dt);

            $weatherData2 = new WeatherData;
            $weatherData2->setDevId('eui-ffffd57ed005ffff');
            $weatherData2->setDatetime(clone $dt);
            $weatherData2->setAppId('weatherdata-lora');
            $weatherData2->setTtnTime($dt->format('Y-m-d-H-i-s') . 'Z');
            $weatherData2->setGtwId('flowerstreet');
            $weatherData2->setGtwRssi(rand(60, 90));
            $weatherData2->setGtwSnr(rand(50, 100) / 10);
            $weatherData2->setDataTemperature(rand(15, 25));
            $weatherData2->setDataHumidity(rand(50, 100));
            $weatherData2->setDataPressure(rand(800, 1100));
            $weatherData2->setDataBattery(rand(35, 41) / 10);
            $manager->persist(($weatherData2));
            $weatherStation2->setLastUpdate(clone $dt);

            $weatherData3 = new WeatherData;
            $weatherData3->setDevId('eui-ffffd57ed005aaaa');
            $weatherData3->setDatetime(clone $dt);
            $weatherData3->setAppId('weatherdata-lora');
            $weatherData3->setTtnTime($dt->format('Y-m-d-H-i-s') . 'Z');
            $weatherData3->setGtwId('flowerstreet');
            $weatherData3->setGtwRssi(rand(60, 90));
            $weatherData3->setGtwSnr(rand(50, 100) / 10);
            $weatherData3->setDataTemperature(rand(15, 25));
            $weatherData3->setDataHumidity(rand(50, 100));
            $weatherData3->setDataPressure(rand(800, 1100));
            $weatherData3->setDataBattery(rand(35, 41) / 10);
            $manager->persist(($weatherData3));
            $weatherStation3->setLastUpdate(clone $dt);


            $weatherData4 = new WeatherData;
            $weatherData4->setDevId('eui-ffffd57ed005bbbb');
            $weatherData4->setDatetime(clone $dt);
            $weatherData4->setAppId('weatherdata-lora');
            $weatherData4->setTtnTime($dt->format('Y-m-d-H-i-s') . 'Z');
            $weatherData4->setGtwId('flowerstreet');
            $weatherData4->setGtwRssi(rand(60, 90));
            $weatherData4->setGtwSnr(rand(50, 100) / 10);
            $weatherData4->setDataTemperature(rand(15, 25));
            $weatherData4->setDataHumidity(rand(50, 100));
            $weatherData4->setDataPressure(rand(800, 1100));
            $weatherData4->setDataBattery(rand(35, 41) / 10);
            $manager->persist(($weatherData4));

            $weatherStation4->setLastUpdate(clone $dt);


            $weatherData5 = new WeatherData;
            $weatherData5->setDevId('eui-ffffd57ed-ignore');
            $weatherData5->setDatetime(clone $dt);
            $weatherData5->setAppId('weatherdata-lora');
            $weatherData5->setTtnTime($dt->format('Y-m-dTH:i:s') . 'Z');
            $weatherData5->setGtwId('flowerstreet');
            $weatherData5->setGtwRssi(rand(60, 90));
            $weatherData5->setGtwSnr(rand(50, 100) / 10);
            $weatherData5->setDataTemperature(rand(15, 25));
            $weatherData5->setDataHumidity(rand(50, 100));
            $weatherData5->setDataPressure(rand(800, 1100));
            $weatherData5->setDataBattery(rand(35, 41) / 10);
            $weatherData5->setDataWind(rand(0, 100) / 10);
            $weatherData5->setDataWinddir(0);
            $weatherData5->setDataBrightness(rand(0, 10000));
            $weatherData5->setDataRadiation(0);
            $weatherData5->setDataRain(rand(0, 5));
            $weatherData5->setDataUv(0);
            $manager->persist(($weatherData5));
            $weatherStation5->setLastUpdate(clone $dt);
        }
        $manager->persist(($weatherStation5));
        $manager->persist(($weatherStation4));
        $manager->persist(($weatherStation3));
        $manager->persist(($weatherStation2));
        $manager->persist(($weatherStation1));


        $manager->flush();
    }
}
