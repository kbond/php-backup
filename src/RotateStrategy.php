<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface RotateStrategy
{
    /**
     * @param BackupCollection $existingBackups
     * @param Backup           $newBackup
     *
     * @return BackupCollection The backups to be removed
     */
    public function getBackupsToRemove(BackupCollection $existingBackups, Backup $newBackup);
}
