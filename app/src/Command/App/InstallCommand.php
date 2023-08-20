<?php

namespace App\Command\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('siotest:install:app')
            ->setDescription('Install DB, run migrations, load fixtures')
            ->setHelp('You can use a command to install Siotest application: DB, migrations, fixtures');
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandList = [
            'doctrine:database:create',
            'doctrine:migrations:migrate',
            'doctrine:fixtures:load'
        ];

        foreach ($commandList as $command) {
            $command = $this->getApplication()->find($command);
            $commandInput = new ArrayInput(['command' => $command]);
            $commandInput->setInteractive(false);
            if ($command->run($commandInput, $output) !== 0) {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}