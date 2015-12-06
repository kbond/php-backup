<?php

namespace Zenstruck\Backup\Destination;

use Psr\Log\LoggerInterface;
use Zenstruck\Backup\Backup;
use Zenstruck\Backup\Destination;
use Zenstruck\Backup\RotateStrategy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RotatedDestination implements Destination
{
    private $destination;
    private $rotateStrategy;

    public function __construct(Destination $destination, RotateStrategy $rotateStrategy)
    {
        $this->destination = $destination;
        $this->rotateStrategy = $rotateStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function push($filename, LoggerInterface $logger)
    {
        $this->doRotate(Backup::fromFile($filename), $logger);

        return $this->destination->push($filename, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->destination->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->destination->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->destination->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->destination->getName();
    }

    /**
     * @param Backup          $newBackup
     * @param LoggerInterface $logger
     */
    private function doRotate(Backup $newBackup, LoggerInterface $logger)
    {
        $backups = $this->all();

        if (0 === count($backups)) {
            return;
        }

        /** @var Backup[] $backupsToRemove */
        $backupsToRemove = $this->rotateStrategy->getBackupsToRemove($backups, $newBackup);

        foreach ($backupsToRemove as $backup) {
            $logger->info(sprintf('Removing backup "%s" from destination "%s"', $backup->getKey(), $this->getName()));
            $this->delete($backup->getKey());
        }
    }
}
