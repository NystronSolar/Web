<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\DayGeneration;
use App\Repository\DayGenerationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
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

        $defaultAdmin = $this->createOneClient(
            'Admin',
            'admin@user.com',
            $this->getFaker()->cpf(false),
            'Admin',
            ['ROLE_USER', 'ROLE_ADMIN'],
            'admin'
        );

        $defaultClient = $this->createOneClient(
            'Client',
            'client@user.com',
            $this->getFaker()->cpf(false),
            'Client',
            ['ROLE_USER'],
            'client'
        );

        $clients = $this->createManyFakerClient(5);
        $clients[] = $defaultAdmin;
        $clients[] = $defaultClient;
        $clients[] = $this->createOneClient(
            'Tim',
            'tim@user.com',
            $this->getFaker()->cpf(false),
            'Tim',
            ['ROLE_USER'],
            'Tim',
            true,
            12
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
    }

    public function hashPassword(Client $client, string $password): string
    {
        $passwordHasher = $this->passwordHasher;

        return $passwordHasher->hashPassword($client, $password);
    }

    /**
     * @param array<int, DayGeneration> $dayGenerations
     */
    public function createOneClient(string $name, string $email, string $cpf, string $growattName, array $roles, string $password, bool $hashPassword = true, int $months = 1): Client
    {
        $months = $months > 12 ? 12 : $months;
        $client = new Client();

        $client->setName($name);
        $client->setEmail($email);
        $client->setCPF($cpf);
        $client->setGrowattName($growattName);
        $client->setRoles($roles);

        if ($hashPassword) {
            $password = $this->hashPassword($client, $password);
        }

        $client->setPassword($password);

        /** @var \App\Repository\ClientRepository $clientRepository */
        $clientRepository = $this->manager->getRepository(Client::class);

        /** @var DayGenerationRepository $dayGenerationRepository */
        $dayGenerationRepository = $this->manager->getRepository(DayGeneration::class);

        $clientRepository->save($client);

        $this->manager->flush();

        for ($i = 1; $i <= $months; ++$i) {
            $month = strtotime("-$i month");
            $month = date('m/Y', $month);
            $dayGenerations = $this->createFakerMonthDayGeneration(\DateTime::createFromFormat('m/Y', $month));

            foreach ($dayGenerations as $dayGeneration) {
                $client->addDayGeneration($dayGeneration);

                $dayGenerationRepository->save($dayGeneration);

                $this->manager->flush();
            }
        }

        return $client;
    }

    /**
     * @return Client[]
     */
    public function createManyFakerClient(int $quantity): array
    {
        $clients = [];

        for ($i = 1; $i <= $quantity; ++$i) {
            $clients[] = $this->createOneFakerClient();
        }

        return $clients;
    }

    public function createOneFakerClient(): Client
    {
        $client = $this->createOneClient(
            $this->getFaker()->name(),
            $this->getFaker()->email(),
            $this->getFaker()->cpf(false),
            $this->getFaker()->userName(),
            ['ROLE_USER'],
            $this->getFaker()->password()
        );

        return $client;
    }

    /**
     * @param array<int, DayGeneration> $dayGenerations
     */
    public function createOneDayGeneration(string $generation, string $hours, \DateTime $date): DayGeneration
    {
        $dayGeneration = new DayGeneration();

        $dayGeneration->setGeneration($generation);
        $dayGeneration->setHours($hours);
        $dayGeneration->setDate($date);

        return $dayGeneration;
    }

    /**
     * @return DayGeneration[]
     */
    public function createFakerMonthDayGeneration(\DateTime $monthDate = new \DateTime()): array
    {
        $year = date_format($monthDate, 'Y');
        $month = date_format($monthDate, 'm');
        $daysInMonth = date_format($monthDate, 't');

        $dayGenerations = [];

        for ($i = 1; $i <= $daysInMonth; ++$i) {
            $date = \DateTime::createFromFormat('d/m/Y', "$i/$month/$year");
            $dayGenerations[] = $this->createOneFakerDayGeneration($date);
        }

        return $dayGenerations;
    }

    public function createOneFakerDayGeneration(\DateTime $date): DayGeneration
    {
        $dayGeneration = $this->createOneDayGeneration(
            (string) $this->getFaker()->randomFloat(1, 5, 20),
            (string) $this->getFaker()->randomFloat(1, 3, 14),
            $date
        );

        return $dayGeneration;
    }
}
