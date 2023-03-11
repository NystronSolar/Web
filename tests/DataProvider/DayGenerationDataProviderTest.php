<?php

namespace App\Tests\DataProvider;

use App\DataProvider\DayGenerationDataProvider;
use App\Entity\DayGeneration;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class DayGenerationDataProviderTest extends TestCase
{
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->any())
            ->method('trans')
            ->willReturn('Translated')
        ;

        $this->translator = $translator;
    }

    protected function createManyFakerDayGeneration(int $quantity): array
    {
        $arr = [];

        for ($i = 1; $i <= $quantity; ++$i) {
            $now = new \DateTime('now');
            $date = $now->modify("$i day");

            $dayGeneration = (new DayGeneration())
                ->setGeneration($i + 1)
                ->setHours($i)
                ->setDate($date)
            ;

            $arr[] = $dayGeneration;
        }

        return $arr;
    }

    protected function createProvider(): DayGenerationDataProvider
    {
        $provider = new DayGenerationDataProvider($this->translator);

        return $provider;
    }

    public function testStyleDayGenerationMethod()
    {
        $provider = $this->createProvider();
        $generations = $this->createManyFakerDayGeneration(5);

        foreach ($generations as $key => $generation) {
            ++$key;
            $styledGeneration = $provider->styleDayGeneration($generation);

            $now = new \DateTime('now');
            $date = $now->modify("$key day");
            $seconds = bcmul($generation->getHours(), 3600);
            $hours = gmdate('H:i', (int) $seconds);

            $this->assertNull($styledGeneration['id']);
            $this->assertSame($date->format('d/m/Y'), $styledGeneration['date']->format('d/m/Y'));
            $this->assertSame((string) ($key + 1), $styledGeneration['generation']);
            $this->assertSame($hours, $styledGeneration['hours']);
        }
    }
}
