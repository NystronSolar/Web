<?php

namespace App\Tests\TestCase;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\Constraint\IsEqual;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class CommandTestCase extends KernelTestCase
{
    private ?KernelInterface $appKernel = null;

    private ?Application $application = null;

    private ?Faker $faker = null;

    private ?Command $command = null;

    abstract protected function getCommandName(): string;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appKernel = $this->appKernel ?? $this->bootKernel();
        $this->application = $this->application ?? $this->customApplication();
        $this->faker = $this->faker ?? FakerFactory::create('pt_BR');

        $this->command = $this->application->find($this->getCommandName());
    }

    protected function customApplication(): Application
    {
        return new Application($this->appKernel);
    }

    protected function getAppKernel(): ?KernelInterface
    {
        return $this->appKernel;
    }

    protected function getApplication(): ?Application
    {
        return $this->application;
    }

    protected function getFaker(): ?Faker
    {
        return $this->faker;
    }

    protected function getCommand(): ?Command
    {
        return $this->command;
    }

    protected function executeCommand(array $inputs): CommandTester
    {
        $commandTester = new CommandTester($this->getCommand());
        $commandTester->setInputs($inputs);
        $commandTester->execute(['command' => $this->getCommandName()]);

        return $commandTester;
    }

    public static function assertCommandSuccess(CommandTester $command, $message = '')
    {
        static::assertEquals(Command::SUCCESS, $command->getStatusCode(), $message);
    }

    public static function assertCommandFailure(CommandTester $command, $message = '')
    {
        static::assertEquals(Command::FAILURE, $command->getStatusCode(), $message);
    }

    public static function assertCommandInvalid(CommandTester $command, $message = '')
    {
        static::assertEquals(Command::INVALID, $command->getStatusCode(), $message);
    }
}
