<?php

namespace Zenstruck\Backup;

use Psr\Log\LoggerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Source
{
    /**
     * @param string $scratchDir Path to the scratch directory
     */
    public function fetch(string $scratchDir, LoggerInterface $logger);

    public function getName(): string;
}
