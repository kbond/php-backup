<?php

namespace Zenstruck\Backup\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zenstruck\Backup\Profile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RunCommand extends ProfileActionCommand
{
    protected function configure(): void
    {
        $this
            ->setName('zenstruck:backup:run')
            ->setDescription('Run a backup profile')
            ->addArgument('profile', InputArgument::OPTIONAL, 'The backup profile to run (leave blank for listing)')
            ->addOption('clear', null, InputOption::VALUE_NONE, 'Set this flag to clear scratch directory before backup')
        ;
    }

    /**
     * @throws \Exception
     */
    protected function doExecute(Profile $profile, InputInterface $input, OutputInterface $output): int
    {
        $this->getBackupHelper()->getExecutor()->backup($profile, $input->getOption('clear'));

        return Command::SUCCESS;
    }
}
