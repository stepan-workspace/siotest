<?php

namespace App\Command\App;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The command starts installation of the application, namely:
 *      create database
 *      make migrations
 *      load fixtures (simple data)
 *
 * To use this command, open a terminal window, enter into
 * your project  directory and execute the following:
 *
 *      $ php bin/console siotest:install:app
 *
 * Before executing this command, you must delete the current
 * database or specify a new database name in the
 * configuration file
 */
#[AsCommand(
    name: 'siotest:install:app',
    description: 'Install DB, make migrations, load fixtures'
)]
final class InstallCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp($this->getCommandHelp());
    }

    /**
     * This method it usually contains the logic to execute to complete this command task.
     *
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandList = [
            'doctrine:database:create',
            'doctrine:migrations:migrate',
            'doctrine:fixtures:load'
        ];

        foreach ($commandList as $item) {
            $command = $this->getApplication()->find($item);
            $commandInput = new ArrayInput(['command' => $command]);
            $commandInput->setInteractive(false);
            if ($command->run($commandInput, $output) !== 0) {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command starts the installation of the application, namely:
                - create <comment>database</comment>
                - make <comment>migrations</comment>
                - load <comment>fixtures</comment> (simple data)
            
            Before executing <info>php %command.full_name%</info> command, you must delete the
            current database (you can execute: <comment>php bin/console doctrine:database:drop --force</comment>)
            or specify a new database name in the configuration files

            HELP;
    }
}