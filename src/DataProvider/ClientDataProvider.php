<?php

namespace App\DataProvider;

use App\Entity\Client;
use App\Utils\Chart\Chart;
use App\Utils\Chart\ChartDataset;
use App\Utils\Chart\Type;

class ClientDataProvider extends DataProvider
{
    public function getClientGenerationChart(Client $client): Chart
    {
        $chart = new Chart();
        $chart->setType(Type::Line);

        $generationDataset = (new ChartDataset())
            ->setBackgroundColor('rgba(54, 162, 235, 0.5)')
            ->setBorderColor('rbg(54, 162, 235)')
            ->setKey('app-energy-generated-chart')
            ->setLabel($this->getTranslator()->trans('base.energy_generated').' (kWh)')
        ;

        $timeDataset = (new ChartDataset())
            ->setBackgroundColor('rgba(255, 99, 132, 0.5)')
            ->setBorderColor('rgb(255, 99, 132)')
            ->setKey('app-time-generated-chart')
            ->setLabel($this->getTranslator()->trans('base.hours_generated'))
        ;

        $datasets = [];
        array_push($datasets, $generationDataset);
        array_push($datasets, $timeDataset);

        foreach ($client->getDayGenerations() as $dayGeneration) {
            $chart->addLabel($dayGeneration->getDate()->format($this->getTranslator()->trans('base.date_format')));

            $generationDataset->addData($dayGeneration->getGeneration());
            $timeDataset->addData($dayGeneration->getHours());
        }

        $chart->addDataset($generationDataset);
        $chart->addDataset($timeDataset);

        return $chart;
    }
}
