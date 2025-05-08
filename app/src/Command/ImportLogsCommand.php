<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\ImportFileMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import-logs',
    description: 'Import logs from a specified file'
)]
class ImportLogsCommand extends Command
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'log-file',
                InputArgument::REQUIRED,
                'Path to the log file to import'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $logFile = $input->getArgument('log-file');
        $logFilePath = new ImportFileMessage($logFile);

        if (!$this->filesystem->exists($logFile)) {
            $errorMsg = "Log file does not exist: {$logFile}";
            $io->error($errorMsg);

            return Command::FAILURE;
        }

        $this->bus->dispatch($logFilePath);

        $io->success("<info>Log file dispatched to queue: {$logFile}</info>");

        return Command::SUCCESS;
    }
}
