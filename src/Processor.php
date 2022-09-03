<?php

namespace Zenstruck\Backup;

use Psr\Log\LoggerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Processor
{
    /**
     * @param string $scratchDir Path to the scratch directory
     *
     * @return string The filename to backup
     */
    public function process(string $scratchDir, Namer $namer, LoggerInterface $logger): string;

    /**
     * @param string $filename The file to cleanup
     */
    public function cleanup(string $filename, LoggerInterface $logger);

    public function getName(): string;
}
