<?php

namespace Zenstruck\Backup\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zenstruck\Backup\Console\Helper\BackupHelper;
use Zenstruck\Backup\Profile;
use Zenstruck\Backup\ProfileRegistry;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class ProfileActionCommand extends BaseCommand
{
    /**
     * Command specific code.
     */
    abstract protected function doExecute(Profile $profile, InputInterface $input, OutputInterface $output): int;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $registry = $this->getBackupHelper()->getProfileRegistry();

        if (!$profile = $input->getArgument('profile')) {
            $this->listProfiles($output, $registry);

            return 0;
        }

        return $this->doExecute($registry->get($profile), $input, $output);
    }

    /**
     * @param ProfileRegistry|Profile[] $registry
     */
    protected function listProfiles(OutputInterface $output, ProfileRegistry|array $registry): void
    {
        if (0 === \count($registry)) {
            throw new \RuntimeException('No profiles configured.');
        }

        $output->writeln('<info>Available Profiles:</info>');
        $output->writeln('');

        $table = new Table($output);
        $table->setHeaders(['Name', 'Processor', 'Namer', 'Sources', 'Destinations']);

        foreach ($registry as $profile) {
            $table->addRow([
                $profile->getName(),
                $profile->getProcessor()->getName(),
                $profile->getNamer()->getName(),
                \implode(', ', \array_keys($profile->getSources())),
                \implode(', ', \array_keys($profile->getDestinations())),
            ]);
        }

        $table->render();
        $output->writeln('');
    }

    protected function getBackupHelper(): BackupHelper
    {
        return $this->getHelper('zenstruck_backup');
    }
}
