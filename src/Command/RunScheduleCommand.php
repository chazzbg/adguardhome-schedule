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

    public function __construct(private readonly ScheduleRunner $runner)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('force-block')
        ->addOption('force-unblock');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->runner->run($input->getOption('force-block'), $input->getOption('force-unblock'));
        return Command::SUCCESS;
    }
}
