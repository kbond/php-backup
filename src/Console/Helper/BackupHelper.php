<?php

namespace Zenstruck\Backup\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use Zenstruck\Backup\Executor;
use Zenstruck\Backup\ProfileRegistry;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class BackupHelper extends Helper
{
    public function __construct(private ProfileRegistry $profileRegistry, private Executor $executor)
    {
    }

    public function getProfileRegistry(): ProfileRegistry
    {
        return $this->profileRegistry;
    }

    public function getExecutor(): Executor
    {
        return $this->executor;
    }

    public function getName(): string
    {
        return 'zenstruck_backup';
    }
}
