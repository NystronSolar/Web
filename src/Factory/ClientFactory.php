<?php

namespace App\Factory;

use App\Entity\Client;
use App\Entity\DayGeneration;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function getPasswordHasher(): UserPasswordHasherInterface
    {
        return $this->passwordHasher;
    }

    public function setPasswordHasher(UserPasswordHasherInterface $passwordHasher): self
    {
        $this->passwordHasher = $passwordHasher;

        return $this;
    }

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

    public static function getRequiredProperties(): array
    {
        return [
            'Name',
            'Email',
            'CPF',
            'GrowattName',
            'Roles',
            'Password',
        ];
    }

    /**
     * @param array<DayGeneration> $dayGenerations
     */
    public function createOne(string $name, string $email, string $cpf, string $growattName, array $roles, string $password, array $dayGenerations = [], bool $hashPassword = true): Client
    {
        $client = (new Client())
            ->setName($name)
            ->setEmail($email)
            ->setCPF($cpf)
            ->setGrowattName($growattName)
            ->setRoles($roles)
        ;

        if ($hashPassword) {
            $password = $this->passwordHasher->hashPassword($client, $password);
        }

        $client->setPassword($password);

        foreach ($dayGenerations as $dayGeneration) {
            if ($dayGeneration instanceof DayGeneration) {
                $client->addDayGeneration($dayGeneration);
            }
        }

        return $client;
    }

    public function createOneByArray(array $data, bool $hashPassword = true): Client|false
    {
        if (!$this->assertDataHasAllKeys($data)) {
            return false;
        }

        $shouldDayGenerations = array_key_exists('DayGenerations', $data);

        $client = $this->createOne(
            name: $data['Name'],
            email: $data['Email'],
            cpf: $data['CPF'],
            growattName: $data['GrowattName'],
            roles: $data['Roles'],
            password: $data['Password'],
            dayGenerations: $shouldDayGenerations ? $data['DayGenerations'] : [],
            hashPassword: $hashPassword
        );

        return $client;
    }
}
