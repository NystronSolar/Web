<?php

namespace App\Tests\Factory;

use App\Entity\Client;
use App\Entity\DayGeneration;
use App\Factory\ClientFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFactoryTest extends TestCase
{
    private ClientFactory $clientFactory;

    private UserPasswordHasherInterface $passwordHasher;

    private array $defaultClient = [
        'Name' => 'Client',
        'Email' => 'client@user.com',
        'CPF' => '84699752004',
        'GrowattName' => 'client',
        'Roles' => ['ROLE_USER'],
        'Password' => 'client',
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

        $this->passwordHasher = $passwordHasher;
        $this->clientFactory = new ClientFactory($this->passwordHasher);
        $this->defaultClient['DayGenerations'] = [(new DayGeneration())->setDate(new \DateTimeImmutable())->setGeneration('30')->setHours('50')];
    }

    /**
     * Run $clientFactory->createOne() or $clientFactory->createOneByArray().
     *
     * @param array  $data The data to be created
     * @param string $by   If should be the method with Parameters or Array (Possible Values: "Array" for createOneByArray() method, and any other string will be executed by createOne() method (With Method Parameters))
     *
     * @return Client
     */
    private function createOneClient(array $data, string $by = '', bool $hashPassword = true): Client|false
    {
        if ('Array' === $by) {
            return $this->clientFactory->createOneByArray($data, $hashPassword);
        }

        return $this->clientFactory->createOne(
            name: $data['Name'],
            email: $data['Email'],
            cpf: $data['CPF'],
            growattName: $data['GrowattName'],
            roles: $data['Roles'],
            password: $data['Password'],
            dayGenerations: $data['DayGenerations'],
            hashPassword: $hashPassword
        );
    }

    public function testByParametersDefaultClient(): void
    {
        // Arrange
        $data = $this->defaultClient;

        // Act
        $client = $this->createOneClient($data, 'Parameters');

        // Assert
        $this->assertSame($data['Name'], $client->getName());
        $this->assertSame($data['Email'], $client->getEmail());
        $this->assertSame($data['CPF'], $client->getCPF());
        $this->assertSame($data['GrowattName'], $client->getGrowattName());
        $this->assertSame($data['Roles'], $client->getRoles());
        $this->assertSame('PasswordHash', $client->getPassword(), 'The Password isn\'t Hashed or are Hashed Incorrectly.');
        $this->assertSame($data['DayGenerations'], $client->getDayGenerations()->toArray());
    }

    public function testByParametersDefaultClientNoHash(): void
    {
        // Arrange
        $data = $this->defaultClient;

        // Act
        $client = $this->createOneClient($data, by: 'Parameters', hashPassword: false);

        // Assert
        $this->assertSame($data['Password'], $client->getPassword(), 'The Password are being Hashed or modified (Parameter bool $hashPassword in CreateOne() method is False.).');
    }

    public function testByParametersDefaultClientNoDaysGenerations(): void
    {
        // Arrange
        $data = $this->defaultClient;
        $data['DayGenerations'] = [];

        // Act
        $client = $this->createOneClient($data, by: 'Parameters');

        // Assert
        $this->assertSame($data['DayGenerations'], $client->getDayGenerations()->toArray());
    }

    public function testByArrayDefaultClient(): void
    {
        // Arrange
        $data = $this->defaultClient;

        // Act
        $client = $this->createOneClient($data, 'Array');

        // Assert
        $this->assertSame($data['Name'], $client->getName());
        $this->assertSame($data['Email'], $client->getEmail());
        $this->assertSame($data['CPF'], $client->getCPF());
        $this->assertSame($data['GrowattName'], $client->getGrowattName());
        $this->assertSame($data['Roles'], $client->getRoles());
        $this->assertSame('PasswordHash', $client->getPassword(), 'The Password isn\'t Hashed or are Hashed Incorrectly.');
        $this->assertSame($data['DayGenerations'], $client->getDayGenerations()->toArray());
    }

    public function testByArrayDefaultClientNoHash(): void
    {
        // Arrange
        $data = $this->defaultClient;

        // Act
        $client = $this->createOneClient($data, by: 'Array', hashPassword: false);

        // Assert
        $this->assertSame($data['Password'], $client->getPassword(), 'The Password are being Hashed or modified (Parameter bool $hashPassword in CreateOne() method is False.).');
    }

    public function testByArrayDefaultClientNoDaysGenerations(): void
    {
        // Arrange
        $data = $this->defaultClient;
        $data['DayGenerations'] = [];

        // Act
        $client = $this->createOneClient($data, by: 'Array');

        // Assert
        $this->assertSame($data['DayGenerations'], $client->getDayGenerations()->toArray());
    }

    public function testByArrayWrongKeys(): void
    {
        // Arrange
        $data = ['Wrong Keys' => true, 'Is this Valid?' => false];

        // Act
        $client = $this->createOneClient($data, by: 'Array');

        // Assert
        $this->assertFalse($client);
    }
}
