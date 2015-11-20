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
    private $maxCount;

    /**
     * @param int $maxCount
     */
    public function __construct($maxCount)
    {
        $this->maxCount = $maxCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackupsToRemove(BackupCollection $existingBackups, Backup $newBackup)
    {
        $count = count($existingBackups) + 1;

        if ($count <= $this->maxCount) {
            return new BackupCollection();
        }

        $backupsToRemove = array();

        for ($i = 0; $i < ($count - $this->maxCount); ++$i) {
            $backupsToRemove[] = $existingBackups->get($i);
        }

        return new BackupCollection($backupsToRemove);
    }
}
