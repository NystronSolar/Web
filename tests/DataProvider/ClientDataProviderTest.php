<?php

namespace App\Tests\DataProvider;

use App\DataProvider\ClientDataProvider;
use App\Entity\Client;
use App\Entity\DayGeneration;
use App\Factory\ClientFactory;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClientDataProviderTest extends TestCase
{
    private ClientFactory $clientFactory;

    private UserPasswordHasherInterface $passwordHasher;

    private Generator $faker;

    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $passwordHasher
            ->expects($this->any())
            ->method('hashPassword')
            ->willReturn('PasswordHash')
        ;

        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->expects($this->any())
            ->method('trans')
            ->willReturn('d/m/Y')
        ;

        $this->passwordHasher = $passwordHasher;
        $this->clientFactory = new ClientFactory($this->passwordHasher);
        $this->faker = Factory::create('pt_BR');
        $this->translator = $translator;
    }

    protected function createOneFakerClient(): Client
    {
        return $this->clientFactory->createOne(
            $this->faker->name(),
            $this->faker->email(),
            $this->faker->cpf(false),
            $this->faker->userName(),
            ['ROLE_USER'],
            'Password',
            []
        );
    }

    protected function createManyFakerDayGeneration(int $quantity, Client &$client): array
    {
        $arr = [];

        for ($i = 1; $i <= $quantity; ++$i) {
            $now = new \DateTime('now');
            $date = $now->modify("$i day");

            $dayGeneration = (new DayGeneration())
                ->setGeneration($i + 1)
                ->setHours($i)
                ->setDate($date)
                ->setClient($client)
            ;

            $arr[] = $dayGeneration;
            $client->addDayGeneration($dayGeneration);
        }

        return $arr;
    }

    protected function createProvider(): ClientDataProvider
    {
        $provider = new ClientDataProvider($this->translator);

        return $provider;
    }

    public function testGetClientGenerationChartMethod()
    {
        $provider = $this->createProvider();
        $client = $this->createOneFakerClient();
        $this->createManyFakerDayGeneration(5, $client);

        $chart = $provider->getClientGenerationChart($client);

        $i = 1;
        foreach ($chart->getLabels() as $label) {
            $now = new \DateTime('now');
            $date = $now->modify("$i day");

            $this->assertSame(
                $date->format('d/m/Y'),
                $label
            );

            ++$i;
        }
    }

    protected function assertArrayHasValidStringDate(array $array, string $format)
    {
        foreach ($array as $value) {
            $date = \DateTime::createFromFormat($format, $value);
            $this->assertTrue($date && ($date->format($format) === $value), sprintf('Date "%s" does not match the format "%s"', $value, $format));
        }
    }
}
