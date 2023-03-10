<?php

namespace App\DataFixtures;

use App\Entity\Bill;
use App\Entity\Client;
use App\Entity\DayGeneration;
use App\Factory\BillFactory;
use App\Factory\ClientFactory;
use App\Repository\BillRepository;
use App\Repository\DayGenerationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Money\Money;
use Symfony\Component\Finder\Finder;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Faker $faker;

    private UserPasswordHasherInterface $passwordHasher;

    private ObjectManager $manager;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;

        $this->faker = FakerFactory::create('pt_BR');
    }

    public function getFaker(): Faker
    {
        return $this->faker;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createOneClient(
            'Admin',
            'admin@user.com',
            $this->getFaker()->cpf(false),
            'Admin',
            ['ROLE_USER', 'ROLE_ADMIN'],
            'admin'
        );

        $clients = [];
        $clients[] = $this->createOneClient(
            'Tim',
            'tim@user.com',
            $this->getFaker()->cpf(false),
            'Tim',
            ['ROLE_USER'],
            'Tim',
            true
        );

        $clients[] = $this->createOneClient(
            'Noah',
            'noah@user.com',
            $this->getFaker()->cpf(false),
            'Noah',
            ['ROLE_USER'],
            'Noah'
        );

        $clients[] = $this->createOneClient(
            'Mia',
            'mia@user.com',
            $this->getFaker()->cpf(false),
            'Mia',
            ['ROLE_USER'],
            'Mia'
        );

        $clients[] = $this->createOneClient(
            'Mary',
            'mary@user.com',
            $this->getFaker()->cpf(false),
            'Mary',
            ['ROLE_USER'],
            'Mary'
        );

        $clients[] = $this->createOneClient(
            'Steven',
            'steven@user.com',
            $this->getFaker()->cpf(false),
            'Steven',
            ['ROLE_USER'],
            'Steven'
        );

        $this->uploadSpreadsheets();
        $this->createBills($clients);
    }

    /**
     * @param array<int, DayGeneration> $dayGenerations
     */
    public function createOneClient(string $name, string $email, string $cpf, string $growattName, array $roles, string $password, bool $hashPassword = true, int $months = 1): Client
    {
        $months = $months > 12 ? 12 : $months;
        $clientFactory = new ClientFactory($this->passwordHasher);
        $client = $clientFactory->createOne(
            name: $name,
            email: $email,
            cpf: $cpf,
            growattName: $growattName,
            roles: $roles,
            password: $password,
            hashPassword: $hashPassword
        );

        /** @var \App\Repository\ClientRepository $clientRepository */
        $clientRepository = $this->manager->getRepository(Client::class);

        $clientRepository->save($client);

        $this->manager->flush();

        return $client;
    }

    public function uploadSpreadsheets()
    {
        /** @var \App\Repository\ClientRepository $clientRepository */
        $clientRepository = $this->manager->getRepository(Client::class);

        /** @var DayGenerationRepository $dayGenerationRepository */
        $dayGenerationRepository = $this->manager->getRepository(DayGeneration::class);

        $finder = new Finder();
        $folder = 'tests/Content/GrowattSpreadsheets';
        $finder->files()->in('tests/Content/GrowattSpreadsheets')->name('*.xlsx');

        for ($i = 1; $i <= $finder->count(); ++$i) {
            $dayGenerationRepository->uploadGrowattSpreadsheet($folder."/Fake$i.xlsx");
        }
    }

    /**
     * @param Client[] $clients
     */
    public function createBills(array $clients, int $quantityPerClient = 3)
    {
        $quantityPerClient = $quantityPerClient > 6 ? 6 : $quantityPerClient;
        $quantityPerClient = $quantityPerClient < 1 ? 1 : $quantityPerClient;

        $billFactory = new BillFactory();

        foreach ($clients as $client) {
            $energyBalance = 0;
            for ($i = 1; $i <= $quantityPerClient; ++$i) {
                $priceNumber = $this->faker->numberBetween(50, 500);
                $priceString = (string) $priceNumber.'00';
                $price = Money::BRL($priceString);

                $energyConsumed = (string) $this->faker->numberBetween(50, 200);
                $energyExcess = $this->faker->numberBetween(100, 500);
                $energyBalance = $energyBalance + $energyExcess;

                $bill = $billFactory->createOne(
                    $price,
                    new \DateTime('now'),
                    new \DateTime('+1 month'),
                    $energyConsumed,
                    $client,
                    $i,
                    2023,
                    new \DateTime('-1 month'),
                    $energyBalance,
                    $energyExcess
                );

                $client->addBill($bill);

                /** @var BillRepository $billRepository */
                $billRepository = $this->manager->getRepository(Bill::class);
                $billRepository->save($bill);
                $this->manager->flush();
            }
        }
    }
}
