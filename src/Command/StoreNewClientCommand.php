<?php

namespace App\Command;

use App\Entity\Client;
use App\Exception\ConsoleException;
use App\Repository\ClientRepository;
use App\Validator\CPF;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:store-new-client',
    description: 'Store a New Client in the Database',
)]
class StoreNewClientCommand extends BaseCommand
{
    private UserPasswordHasherInterface $passwordHasher;
    private ClientRepository $clientRepository;
    private ValidatorInterface $validator;
    private Client $client;

    /** @var Constraint|Constraint[] */
    private array $constraints;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ClientRepository $clientRepository, ValidatorInterface $validator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->clientRepository = $clientRepository;
        $this->validator = $validator;

        $this->constraints = [
            'CPF' => new CPF(),
            'Email' => new Email(),
        ];

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $this->client = new Client();
        $this->io->title('Store a New Client in the Database');

        try {
            $this
                ->askEmail()
                ->askName()
                ->askCPF()
                ->askPassword()
                ->askRoles()
                ->confirmClientInfo()
                ->saveClient()
            ;
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->io->success('Client Stored in Database!');

        return Command::SUCCESS;
    }

    private function validate(string $exceptionMessage, Client $client = null)
    {
        $client = $client ?? $this->client;
        $validator = $this->validator->validate($this->client);

        if ($validator->count() > 0) {
            throw new ConsoleException($exceptionMessage);
        }
    }

    private function askEmail(): self
    {
        $email = $this->askString('The Client Email', 'Email');

        $this->client->setEmail($email);

        $this->validate(sprintf('The Email %s is not a valid Email.', $email));

        return $this;
    }

    private function askName(): self
    {
        $name = $this->askString('The Client Name', 'Name');

        $this->client->setName($name);

        $this->validate(sprintf('The Name %s is not a valid Name.', $name));

        return $this;
    }

    private function askCPF(): self
    {
        $cpf = $this->askString('The Client CPF', 'CPF');

        $this->client->setCPF($cpf);

        $this->validate(sprintf('The CPF %s is not a valid CPF.', $cpf));

        return $this;
    }

    private function askPassword(): self
    {
        $password = $this->askString('The Client Password', 'Password', hide: true);
        $confirmPassword = $this->askString('Confirm The Client Password', 'Password', hide: true);

        $confirmPwd = $password === $confirmPassword;

        if (!$confirmPwd) {
            throw new ConsoleException('The Passwords are different');
        }

        $passwordHash = $this->passwordHasher->hashPassword($this->client, $password);

        $this->client->setPassword($passwordHash);

        return $this;
    }

    private function askRoles(): self
    {
        $roles = [];

        while (true) {
            $newRole = $this->askRole();

            // If client don't want to add new role
            if (!$newRole) {
                break;
            }

            $roles[] = $newRole;
        }

        $this->client->setRoles($roles);

        return $this;
    }

    private function askRole(): mixed
    {
        $role = $this->io->ask('The Client Role (Press <return> to Stop Adding Roles)');

        return $role;
    }

    private function getRolesString(array $roles): string
    {
        $str = '';
        $len = sizeof($roles) - 1;

        foreach ($roles as $key => $value) {
            $str .= $key === $len ? $value : sprintf('%s, ', $value);
        }

        return $str;
    }

    private function confirmClientInfo(): self
    {
        $this->io->section('Confirm Client Data');
        $client = $this->client;

        $roles = $this->getRolesString($client->getRoles());

        $this->io->listing([
            'Email: '.$client->getEmail(),
            'Name: '.$client->getName(),
            'CPF: '.$client->getCPF(),
            'Roles: '.$roles,
        ]);

        $confirm = $this->io->confirm('Confirm');

        if (!$confirm) {
            throw new ConsoleException('Client information not confirmed!');
        }

        return $this;
    }

    public function saveClient(bool $flush = true): self
    {
        $this->clientRepository->save($this->client, $flush);

        return $this;
    }
}
