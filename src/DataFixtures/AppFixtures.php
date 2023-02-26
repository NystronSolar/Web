<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Faker $faker;

    private UserPasswordHasherInterface $passwordHasher;

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
            'Client',
            $this->getFaker()->cpf(false),
            ['ROLE_USER'],
            'client'
        );

        $clients = $this->createManyFakerClient(5);
        $clients[] = $defaultAdmin;
        $clients[] = $defaultClient;

        /** @var \App\Repository\ClientRepository $clientRepository */
        $clientRepository = $manager->getRepository(Client::class);
        foreach ($clients as $client) {
            $clientRepository->save($client);
        }

        $manager->flush();
    }

    public function hashPassword(Client $client, string $password): string
    {
        $passwordHasher = $this->passwordHasher;

        return $passwordHasher->hashPassword($client, $password);
    }

    public function createOneClient(string $name, string $email, string $cpf, string $growattName, array $roles, string $password, bool $hashPassword = true): Client
    {
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
}
