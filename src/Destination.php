<?php

namespace Zenstruck\Backup;

use Psr\Log\LoggerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Destination
{
    /**
     * @param string          $filename
     * @param LoggerInterface $logger
     *
     * @return Backup
     */
    public function push($filename, LoggerInterface $logger);

    /**
     * @param string $key
     *
     * @return Backup
     */
    public function get($key);

    /**
     * @param $key
     */
    public function delete($key);

    /**
     * @return BackupCollection
     */
    public function all();

    /**
     * @return string
     */
    public function getName();
}
