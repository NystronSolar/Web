<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\DayGeneration;
use App\Factory\ClientFactory;
use App\Repository\DayGenerationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
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

        $this->createOneClient(
            'Tim',
            'tim@user.com',
            $this->getFaker()->cpf(false),
            'Tim',
            ['ROLE_USER'],
            'Tim',
            true
        );

        $this->createOneClient(
            'Noah',
            'noah@user.com',
            $this->getFaker()->cpf(false),
            'Noah',
            ['ROLE_USER'],
            'Noah'
        );

        $this->createOneClient(
            'Mia',
            'mia@user.com',
            $this->getFaker()->cpf(false),
            'Mia',
            ['ROLE_USER'],
            'Mia'
        );

        $this->createOneClient(
            'Mary',
            'mary@user.com',
            $this->getFaker()->cpf(false),
            'Mary',
            ['ROLE_USER'],
            'Mary'
        );

        $this->createOneClient(
            'Steven',
            'steven@user.com',
            $this->getFaker()->cpf(false),
            'Steven',
            ['ROLE_USER'],
            'Steven'
        );

        $this->uploadSpreadsheets();
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
}
