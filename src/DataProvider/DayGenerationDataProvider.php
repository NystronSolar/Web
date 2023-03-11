<?php

namespace App\DataProvider;

use App\Entity\DayGeneration;

class DayGenerationDataProvider extends DataProvider
{
    public function styleDayGeneration(DayGeneration $dayGeneration): array
    {
        $seconds = bcmul($dayGeneration->getHours(), 3600);
        $hours = gmdate('H:i', (int) $seconds);

        return [
            'id' => $dayGeneration->getId(),
            'date' => $dayGeneration->getDate(),
            'generation' => $dayGeneration->getGeneration(),
            'hours' => $hours,
        ];
    }
}
