<?php

namespace App\Factory;

use App\Entity\Bill;
use App\Entity\Client;
use Money\Money;

class BillFactory
{

    public static function assertDataHasAllKeys(array $data, array $properties = []): bool
    {
        $properties = [] === $properties ? static::getRequiredProperties() : $properties;

        foreach ($properties as $property) {
            if (!array_key_exists($property, $data)) {
                return false;
            }
        }

        return true;
    }

    public function getProperties()
    {
        return [
            'Price',
            'ActualReadingDate',
            'NextReadingDate',
            'EnergyConsumed',
            'Client',
            'DateMonth',
            'DateYear',
            'PreviousReadingDate',
            'GenerationBalance',
            'EnergyExcess',
        ];
    }

    public static function getRequiredProperties(): array
    {
        return [
            'Price',
            'ActualReadingDate',
            'NextReadingDate',
            'EnergyConsumed',
            'Client',
            'DateMonth',
            'DateYear',
        ];
    }

    public function createOne(Money $price, \DateTimeInterface $actualReadingDate, \DateTimeInterface $nextReadingDate, string $energyConsumed, Client $client, int $dateMonth, int $dateYear, \DateTimeInterface $previousReadingDate = null, string $generationBalance = null, string $energyExcess = null): Bill|false
    {
        $dateMonth = $dateMonth > 12 ? 12 : $dateMonth;
        $dateMonth = $dateMonth < 1 ? 1 : $dateMonth;

        if ($previousReadingDate >= $actualReadingDate || $nextReadingDate <= $actualReadingDate || $price->getCurrency()->getCode() !== "BRL") {
            return false;
        }

        $bill = (new Bill())
            ->setPrice($price->getAmount())
            ->setActualReadingDate($actualReadingDate)
            ->setNextReadingDate($nextReadingDate)
            ->setEnergyConsumed($energyConsumed)
            ->setClient($client)
            ->setDateMonth($dateMonth)
            ->setDateYear($dateYear)
        ;

        if (!is_null($previousReadingDate)) {
            $bill->setPreviousReadingDate($previousReadingDate);
        }

        if (!is_null($generationBalance)) {
            $bill->setGenerationBalance($generationBalance);
        }

        if (!is_null($energyExcess)) {
            $bill->setEnergyExcess($energyExcess);
        }

        return $bill;
    }

    public function createOneByArray(array $data): Bill|false
    {
        if (!$this->assertDataHasAllKeys($data)) {
            return false;
        }

        $shouldPreviousReadingDate = array_key_exists('PreviousReadingDate', $data);
        $shouldGenerationBalance = array_key_exists('GenerationBalance', $data);
        $shouldEnergyExcess = array_key_exists('EnergyExcess', $data);

        $bill = $this->createOne(
        price: $data['Price'],
        actualReadingDate: $data['ActualReadingDate'],
        nextReadingDate: $data['NextReadingDate'],
        energyConsumed: $data['EnergyConsumed'],
        client: $data['Client'],
        dateMonth: $data['DateMonth'],
        dateYear: $data['DateYear'],
        previousReadingDate: $shouldPreviousReadingDate ? $data['PreviousReadingDate'] : null,
        generationBalance: $shouldGenerationBalance ? $data['GenerationBalance'] : null,
        energyExcess: $shouldEnergyExcess ? $data['EnergyExcess'] : null,
        );

        return $bill;
    }

    public function update(Bill $bill, array $data): Bill
    {
        $properties = static::getProperties();

        foreach ($data as $property => $value) {
            $isPrice = 'Price' === $property;

            if ($isPrice) {
                $price = $value->getAmount();
                $bill->setPrice($price);

                continue;
            }

            if (in_array($property, $properties)) {
                $setMethod = 'set' . $property;

                $bill->$setMethod($value);
            }
        }

        return $bill;
    }
}