<?php

namespace Zenstruck\Backup;

use Psr\Log\LoggerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Source extends HasName
{
    /**
     * @param string          $scratchDir Path to the scratch directory
     * @param LoggerInterface $logger
     */
    public function fetch($scratchDir, LoggerInterface $logger);
}
