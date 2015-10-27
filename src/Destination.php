<?php

namespace Zenstruck\Backup;

use Psr\Log\LoggerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Destination extends HasName
{
    /**
     * @param string          $filename
     * @param LoggerInterface $logger
     */
    public function push($filename, LoggerInterface $logger);
}
