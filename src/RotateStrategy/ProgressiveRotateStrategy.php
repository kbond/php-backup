<?php

namespace Zenstruck\Backup\RotateStrategy;

use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\RotateStrategy;

/**
 * @author Alexander Kachkaev <alexander@kachkaev.ru>
 */
class ProgressiveRotateStrategy implements RotateStrategy
{
    private $maxAgeTolerance = 120; // Adds this number of seconds to backup expiry to tolerate backup generation time
    private $ruleCollection;

    /**
     * @param ProgressiveRotateRuleCollection $ruleCollection
     */
    public function __construct(ProgressiveRotateRuleCollection $ruleCollection)
    {
        $this->ruleCollection = $ruleCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackupsToRemove(BackupCollection $backups, Backup $newBackup)
    {
        $now = new \DateTime();

        $ruleCount = count($this->ruleCollection);
        $earliestBackupsInBinsByRules = array();

        for ($i = 0; $i < $ruleCount; ++$i) {
            $rule = $this->ruleCollection->get($i);
            $earliestBackupsInBinsByRules[$i] = array();

            foreach ($backups as $backup) {
                if ($rule->shouldKeep($backup, $now, $this->maxAgeTolerance)) {
                    $bin = $rule->extractBin($backup);

                    if (!array_key_exists($bin, $earliestBackupsInBinsByRules[$i])) {
                        $earliestBackupsInBinsByRules[$i][$bin] = $backup;
                    } else {
                        $currentEarliestBackupInTimeBin = $earliestBackupsInBinsByRules[$i][$bin];

                        if ($backup->getCreatedAt() < $currentEarliestBackupInTimeBin->getCreatedAt()) {
                            $earliestBackupsInBinsByRules[$i][$bin] = $backup;
                        }
                    }
                }
            }
        }

        $backupsToKeep = array();
        for ($i = 0; $i < $ruleCount; ++$i) {
            $backupsToKeep = array_merge($backupsToKeep, array_values($earliestBackupsInBinsByRules[$i]));
        }
        $backupsToKeep = array_unique($backupsToKeep, SORT_REGULAR);

        $backupsToRemove = array();
        foreach ($backups as $backup) {
            if (!in_array($backup, $backupsToKeep)) {
                $backupsToRemove [] = $backup;
            }
        }

        return new BackupCollection($backupsToRemove);
    }
}
