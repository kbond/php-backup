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
     * @return Backup[] Ordered by created at.
     */
    public function all();

    /**
     * @return string
     */
    public function getName();
}
