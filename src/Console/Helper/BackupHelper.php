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
    private $profileRegistry;
    private $executor;

    public function __construct(ProfileRegistry $profileRegistry, Executor $executor)
    {
        $this->profileRegistry = $profileRegistry;
        $this->executor = $executor;
    }

    /**
     * @return ProfileRegistry
     */
    public function getProfileRegistry()
    {
        return $this->profileRegistry;
    }

    /**
     * @return Executor
     */
    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zenstruck_backup';
    }
}
