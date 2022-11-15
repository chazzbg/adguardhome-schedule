<?php

namespace App\Command;

use Ahc\Cron\Expression;
use App\Service\ScheduleRunner;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:run-schedule',
    description: 'Add a short description for your command',
)]
class RunScheduleCommand extends Command
{

    public function __construct( private LoggerInterface $logger , private ScheduleRunner $runner)
    {
        parent::__construct();
    }

    protected function configure(): void
    {

        $this->addOption('dry-run')
            ->addOption('force');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('Schedule has started');
        $this->runner->run($input->hasOption('dry-run'), $input->hasOption('force'));
        $this->logger->info('Schedule has executed');
        return Command::SUCCESS;
    }
}
