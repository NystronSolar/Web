<?php

namespace App\Command;

use App\Exception\ConsoleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BaseCommand extends Command
{
    protected SymfonyStyle $io;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        return Command::SUCCESS;
    }

    protected function askString(string $question, string $type, bool $hide = false): mixed
    {
        $validator = function (mixed $value) use ($type) {
            if ('' === trim((string) $value)) {
                throw new ConsoleException($type.' cannot be empty!');
            }

            return $value;
        };

        $askMethod = $hide ? 'askHidden' : 'ask';
        $response = $this->io->$askMethod($question, validator: $validator);

        return $response;
    }
}
