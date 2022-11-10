<?php

namespace Zenstruck\Backup;

use Psr\Log\LoggerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Destination
{
    public function push(string $filename, LoggerInterface $logger): Backup;

    public function get(string $key): Backup;

    public function delete(string $key);

    public function all(): BackupCollection;

    public function getName(): string;
}
