<?php

namespace App\DataProvider;

use App\Entity\DayGeneration;

class DayGenerationDataProvider extends DataProvider
{
    public function styleDayGeneration(DayGeneration $dayGeneration, bool $styleDate = false): array
    {
        $generationDate = $dayGeneration->getDate();
        $date = $styleDate ? $this->styleDate($generationDate) : $generationDate;

        return [
            'id' => $dayGeneration->getId(),
            'date' => $date,
            'generation' => $dayGeneration->getGeneration(),
            'hours' => $this->styleTime($dayGeneration->getHours()),
            'client_id' => $dayGeneration->getClient()->getId(),
        ];
    }

    /**
     * @param DayGeneration[] $dayGenerations
     *
     * @return array<DayGeneration|array>
     */
    public function groupByMonth(array $dayGenerations, bool $style = true, bool $styleDate = false): array
    {
        $currentGeneration = '0';
        $currentTime = '0';
        $daysCounter = 0;
        $group = [];

        foreach ($dayGenerations as $dayGeneration) {
            ++$daysCounter;

            $currentGeneration = bcadd($currentGeneration, $dayGeneration->getGeneration(), 1);
            $currentTime = bcadd($currentTime, $dayGeneration->getHours(), 1);

            $dayGenerationDate = $dayGeneration->getDate();
            $daysInMonth = $dayGenerationDate->format('t');

            // If is the last day of the month, add the month to Summary and reset the counters ($currentGeneration and $currentTime)
            if ($daysInMonth == $daysCounter) {
                $date = $styleDate ? $this->styleDate($dayGenerationDate) : $dayGenerationDate;

                $group[] = [
                    'date' => $date,
                    'monthGeneration' => $currentGeneration,
                    'monthHours' => $style ? $this->styleTime($currentTime) : $currentTime,
                ];

                $daysCounter = 0;
                $currentGeneration = '0';
                $currentTime = '0';
            }
        }

        $group = array_reverse($group);

        return $group;
    }

    protected function styleTime(string $hours): string
    {
        $seconds = (int) bcmul($hours, 3600);
        $minutes = (int) gmdate('i', (int) $seconds);
        $hours = (int) gmdate('H', (int) $seconds);
        $days = (int) gmdate('d', (int) $seconds) - 1;

        $totalHours = $hours + ($days * 24);

        return $totalHours.':'.$minutes;
    }

    protected function styleDate(\DateTime $date): array
    {
        $month = $date->format('m');
        $year = $date->format('Y');

        return [
            'month' => $month,
            'year' => $year,
        ];
    }
}
