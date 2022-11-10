<?php

namespace Zenstruck\Backup\Destination;

use Psr\Log\LoggerInterface;
use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\Destination;
use Zenstruck\Backup\RotateStrategy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RotatedDestination implements Destination
{
    public function __construct(private Destination $destination, private RotateStrategy $rotateStrategy)
    {
    }

    public function push(string $filename, LoggerInterface $logger): Backup
    {
        $this->doRotate(Backup::fromFile($filename), $logger);

        return $this->destination->push($filename, $logger);
    }

    public function get(string $key): Backup
    {
        return $this->destination->get($key);
    }

    public function delete($key)
    {
        $this->destination->delete($key);
    }

    public function all(): BackupCollection
    {
        return $this->destination->all();
    }

    public function getName(): string
    {
        return $this->destination->getName();
    }

    private function doRotate(Backup $newBackup, LoggerInterface $logger)
    {
        $backups = $this->all();

        if (0 === \count($backups)) {
            return;
        }

        /** @var Backup[] $backupsToRemove */
        $backupsToRemove = $this->rotateStrategy->getBackupsToRemove($backups, $newBackup);

        foreach ($backupsToRemove as $backup) {
            $logger->info(\sprintf('Removing backup "%s" from destination "%s"', $backup->getKey(), $this->getName()));
            $this->delete($backup->getKey());
        }
    }
}
