<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface RotateStrategy
{
    /**
     * @return BackupCollection The backups to be removed
     */
    public function getBackupsToRemove(BackupCollection $existingBackups, Backup $newBackup): BackupCollection;
}
