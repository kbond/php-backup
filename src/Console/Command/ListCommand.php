<?php

namespace Zenstruck\Backup\Console\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zenstruck\Backup\Destination;
use Zenstruck\Backup\Profile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ListCommand extends ProfileActionCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('zenstruck:backup:list')
            ->setDescription('List existing backups for a profile')
            ->addArgument('profile', InputArgument::OPTIONAL, 'The backup profile to list backups for (leave blank for listing)')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Profile $profile, InputInterface $input, OutputInterface $output): int
    {
        foreach ($profile->getDestinations() as $destination) {
            $this->listBackups($destination, $output);
        }

        return 0;
    }

    private function listBackups(Destination $destination, OutputInterface $output)
    {
        $output->writeln(sprintf('Existing backups for <info>%s</info>:', $destination->getName()));
        $output->writeln('');

        $table = new Table($output);
        $table->setHeaders(array('Key', 'Size', 'Created At'));

        $backups = $destination->all();

        if (empty($backups)) {
            $output->writeln(sprintf("<error>No backups for %s</error>\n", $destination->getName()));

            return;
        }

        foreach ($destination->all() as $backup) {
            $table->addRow(array($backup->getKey(), $backup->getFormattedSize(), $backup->getCreatedAt()->format('Y-m-d H:i:s')));
        }

        $table->render();
        $output->writeln('');
    }
}
