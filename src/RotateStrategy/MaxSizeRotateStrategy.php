<?php

namespace Zenstruck\Backup\RotateStrategy;

use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\RotateStrategy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MaxSizeRotateStrategy implements RotateStrategy
{
    private $maxSize;

    /**
     * @param int $maxSize in bytes
     */
    public function __construct($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackupsToRemove(BackupCollection $existingBackups, Backup $newBackup)
    {
        $size = $existingBackups->getTotalFileSize() + $newBackup->getSize();

        if ($size < $this->maxSize) {
            return new BackupCollection();
        }

        $backupsToRemove = array();

        /** @var Backup[] $existingBackups */
        foreach ($existingBackups as $backup) {
            $backupsToRemove[] = $backup;
            $size -= $backup->getSize();

            if ($size < $this->maxSize) {
                break;
            }
        }

        return new BackupCollection($backupsToRemove);
    }
}
