<?php

namespace App\Tests\Factory;

use App\Factory\BillFactory;
use App\Factory\ClientFactory;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BillFactoryTest extends TestCase
{
    private BillFactory $billFactory;

    private array $defaultBill = [
        'EnergyConsumed' => '1000',
    ];

    private array $fullBill = [
        'GenerationBalance' => '10000',
        'EnergyExcess' => '5000'
    ];

    private array $extraBill = [
        'EnergyConsumed' => '10000',
        'GenerationBalance' => '50000',
        'EnergyExcess' => '10000'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $passwordHasher
            ->expects($this->any())
            ->method('hashPassword')
            ->willReturn('PasswordHash')
        ;

        $clientFactory = new ClientFactory($passwordHasher);
        $client = $clientFactory->createOneByArray([
            'Name' => 'Client',
            'Email' => 'client@user.com',
            'CPF' => '84699752004',
            'GrowattName' => 'client',
            'Roles' => ['ROLE_USER'],
            'Password' => 'client',
        ]);

        $billDate = new \DateTime('now');
        $this->billFactory = new BillFactory();
        $this->defaultBill['Price'] = Money::BRL('10000');
        $this->defaultBill['ActualReadingDate'] = new \DateTime('now');
        $this->defaultBill['NextReadingDate'] = new \DateTime('+1 month');
        $this->defaultBill['Client'] = $client;
        $this->defaultBill['DateMonth'] = (int) $billDate->format('m');
        $this->defaultBill['DateYear'] = (int) $billDate->format('Y');

        $this->fullBill['PreviousReadingDate'] = new \DateTime('-1 month');
        $this->fullBill = array_merge($this->fullBill, $this->defaultBill);

        $billDate = new \DateTime('-1 month');
        $this->extraBill['Price'] = Money::BRL('100000');
        $this->extraBill['ActualReadingDate'] = new \DateTime('-1 month');
        $this->extraBill['NextReadingDate'] = new \DateTime('now');
        $this->extraBill['Client'] = $client;
        $this->extraBill['DateMonth'] = (int) $billDate->format('m');
        $this->extraBill['DateYear'] = (int) $billDate->format('Y');
        $this->extraBill['PreviousReadingDate'] = new \DateTime('-2 month');
    }

    public function testCreateOne()
    {
        $billData = $this->fullBill;

        $response = $this->billFactory->createOneByArray($billData);
        $billData['Price'] = $billData['Price']->getAmount();

        $this->assertNotFalse($response);

        foreach ($billData as $property => $value) {
            $getter = "get$property";
            $this->assertSame($value, $response->$getter(), "Property $property is incorrect");
        }
    }

    public function testPriceWithDifferentCurrency()
    {
        $billData = $this->defaultBill;
        $billData['Price'] = Money::EUR('1000');

        $response = $this->billFactory->createOneByArray($billData);

        $this->assertFalse($response);
    }

    public function testWithIncorrectPreviousReadingDate()
    {
        $billData = $this->defaultBill;
        $billData['PreviousReadingDate'] = new \DateTime('+1 month');

        $response = $this->billFactory->createOneByArray($billData);

        $this->assertFalse($response);
    }

    public function testWithIncorrectNextReadingDate()
    {
        $billData = $this->defaultBill;
        $billData['NextReadingDate'] = new \DateTime('-1 month');

        $response = $this->billFactory->createOneByArray($billData);

        $this->assertFalse($response);
    }

    public function testNegativeDateMonth()
    {
        $billData = $this->defaultBill;
        $billData['DateMonth'] = -10;

        $response = $this->billFactory->createOneByArray($billData);

        $this->assertSame(1, $response->getDateMonth());
    }

    public function testBigDateMonth()
    {
        $billData = $this->defaultBill;
        $billData['DateMonth'] = 30;

        $response = $this->billFactory->createOneByArray($billData);

        $this->assertSame(12, $response->getDateMonth());
    }

    public function testUpdate()
    {
        $fullBillData = $this->fullBill;
        $extraBillData = $this->extraBill;

        $bill = $this->billFactory->createOneByArray($fullBillData);
        $response = $this->billFactory->update($bill, $extraBillData);
        $extraBillData['Price'] = $extraBillData['Price']->getAmount();

        $this->assertNotFalse($response);

        foreach ($extraBillData as $property => $value) {
            $getter = "get$property";
            $this->assertSame($value, $response->$getter(), "Property $property is incorrect");
        }
    }
}