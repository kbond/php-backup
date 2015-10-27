<?php

namespace Zenstruck\Backup\Destination;

use Psr\Log\LoggerInterface;
use Zenstruck\Backup\Destination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class StreamDestination implements Destination
{
    private $name;
    private $directory;

    /**
     * @param string $name
     * @param string $directory
     */
    public function __construct($name, $directory)
    {
        $this->name = $name;
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
    public function getName()
    {
        return $this->name;
    }
}
