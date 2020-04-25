<?php

namespace Zenstruck\Backup\Console\Command;

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
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('zenstruck:backup:run')
            ->setDescription('Run a backup profile')
            ->addArgument('profile', InputArgument::OPTIONAL, 'The backup profile to run (leave blank for listing)')
            ->addOption('clear', null, InputOption::VALUE_NONE, 'Set this flag to clear scratch directory before backup')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Profile $profile, InputInterface $input, OutputInterface $output)
    {
        $this->getBackupHelper()->getExecutor()->backup($profile, $input->getOption('clear'));

        return 0;
    }
}
