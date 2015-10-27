<?php

namespace Zenstruck\Backup\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zenstruck\Backup\Console\Helper\BackupHelper;
use Zenstruck\Backup\HasName;
use Zenstruck\Backup\ProfileRegistry;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class BackupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('zenstruck:backup')
            ->setDescription('Run a backup')
            ->addArgument('profile', InputArgument::OPTIONAL, 'The backup profile to run (leave blank for listing)')
            ->addOption('clear', null, InputOption::VALUE_NONE, 'Set this flag to clear scratch directory before backup')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var BackupHelper $helper */
        $helper = $this->getHelper('zenstruck_backup');
        $registry = $helper->getProfileRegistry();
        $executor = $helper->getExecutor();

        if (!$profile = $input->getArgument('profile')) {
            $this->listProfiles($output, $registry);

            return;
        }

        $profile = $registry->get($profile);
        $executor->backup($profile, $input->getOption('clear'));
    }

    private function listProfiles(OutputInterface $output, ProfileRegistry $registry)
    {
        $output->writeln('<info>Available Profiles:</info>');
        $output->writeln('');

        $table = new Table($output);
        $table->setHeaders(array('Name', 'Processor', 'Namer', 'Sources', 'Destinations'));

        foreach ($registry->all() as $name => $profile) {
            $table->addRow(array(
                $name,
                $profile->getProcessor()->getName(),
                $profile->getNamer()->getName(),
                $this->stringify($profile->getSources()),
                $this->stringify($profile->getDestinations()),
            ));
        }

        $table->render();
        $output->writeln('');
    }

    /**
     * @param HasName[] $names
     *
     * @return string
     */
    private function stringify(array $names)
    {
        return implode(', ', array_map(function (HasName $name) {
            return $name->getName();
        }, $names));
    }
}
