<?php

namespace App\Command;

use Ahc\Cron\Expression;
use App\Service\ScheduleRunner;
use Doctrine\Persistence\ManagerRegistry;
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
    private ScheduleRunner $runner;

    public function __construct(ScheduleRunner $runner)
    {
        $this->runner = $runner;
        parent::__construct();
    }

    protected function configure(): void
    {

        $this->addOption('dry-run')
            ->addOption('force');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->runner->run($input->hasOption('dry-run'), $input->hasOption('force'));

        return Command::SUCCESS;
    }
}
