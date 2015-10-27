<?php

namespace Zenstruck\Backup\Destination;

use Psr\Log\LoggerInterface;
use Zenstruck\Backup\Destination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class StreamDestination implements Destination
{
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function push($filename, LoggerInterface $logger)
    {
        $logger->info(sprintf('Copying %s to %s', $filename, $this->directory));

        copy($filename, sprintf('%s/%s', $this->directory, basename($filename)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'stream';
    }
}
