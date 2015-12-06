<?php

namespace Zenstruck\Backup\RotateStrategy;

use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\RotateStrategy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ChainRotateStrategy implements RotateStrategy
{
    /** @var RotateStrategy[] */
    private $rotateStrategies;

    /**
     * @param RotateStrategy[] $rotateStrategies
     */
    public function __construct(array $rotateStrategies)
    {
        $this->rotateStrategies = $rotateStrategies;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackupsToRemove(BackupCollection $existingBackups, Backup $newBackup)
    {
        foreach ($this->rotateStrategies as $rotateStrategy) {
            $backupsToRemove = $rotateStrategy->getBackupsToRemove($existingBackups, $newBackup);

            if (0 !== count($backupsToRemove)) {
                return $backupsToRemove;
            }
        }

        return new BackupCollection();
    }
}
