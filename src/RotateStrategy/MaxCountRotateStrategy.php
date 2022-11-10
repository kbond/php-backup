<?php

namespace Zenstruck\Backup\RotateStrategy;

use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\RotateStrategy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MaxCountRotateStrategy implements RotateStrategy
{
    public function __construct(private int $maxCount)
    {
    }

    public function getBackupsToRemove(BackupCollection $existingBackups, Backup $newBackup): BackupCollection
    {
        $count = \count($existingBackups) + 1;

        if ($count <= $this->maxCount) {
            return new BackupCollection();
        }

        $backupsToRemove = [];

        for ($i = 0; $i < ($count - $this->maxCount); ++$i) {
            $backupsToRemove[] = $existingBackups->get($i);
        }

        return new BackupCollection($backupsToRemove);
    }
}
