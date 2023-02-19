<?php

namespace App\Tests\Command;

use App\Command\StoreNewClientCommand;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Tests\TestCase\CommandTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StoreNewClientCommandTest extends CommandTestCase
{
    protected function getCommandName(): string
    {
        return 'app:store-new-client';
    }

    protected function customApplication(): Application
    {
        $application = new Application($this->getAppKernel());

        $clientRepository = $this->createMock(ClientRepository::class);
        $validator = static::getContainer()->get(ValidatorInterface::class);
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $application->add(new StoreNewClientCommand($passwordHasher, $clientRepository, $validator));

        return $application;
    }

    protected function getConfirmedClient(CommandTester $command): Client
    {
        $output = $command->getDisplay();
        $outputArray = explode(PHP_EOL, $output);

        $client = null;

        foreach ($outputArray as $key => $value) {
            if (str_starts_with($value, 'Confirm Client Data')) {
                $client = new Client();
                $client->setEmail(substr($outputArray[$key + 3], 10));
                $client->setName(substr($outputArray[$key + 4], 9));
                $client->setCPF(substr($outputArray[$key + 5], 8));
                $rolesStr = substr($outputArray[$key + 6], 10);

                $rolesArr = [];
                foreach (explode(', ', $rolesStr) as $value) {
                    $rolesArr[] = $value;
                }

                $client->setRoles($rolesArr);
            }
        }

        return $client;
    }

    public function testSuccessfulCommand()
    {
        $clientExpected = new Client();
        $clientExpected->setEmail('admin@user.com');
        $clientExpected->setName('Admin');
        $clientExpected->setCPF($this->getFaker()->cpf());
        $clientExpected->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $command = $this->executeCommand([
            $clientExpected->getEmail(),
            $clientExpected->getName(),
            $clientExpected->getCPF(),
            'admin',
            'admin',
            ...$clientExpected->getRoles(),
            '',
        ]);

        $clientConfirmed = $this->getConfirmedClient($command);
        $this->assertEquals($clientExpected, $clientConfirmed);
        $this->assertCommandSuccess($command);
    }
}
