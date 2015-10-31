<?php

namespace Zenstruck\Backup\Destination;

use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Zenstruck\Backup\Backup;
use Zenstruck\Backup\Destination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FlysystemDestination implements Destination
{
    private $name;
    private $filesystem;

    /**
     * @param string              $name
     * @param FilesystemInterface $filesystem
     */
    public function __construct($name, FilesystemInterface $filesystem)
    {
        $this->name = $name;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function push($filename, LoggerInterface $logger)
    {
        $key = basename($filename);
        $resource = fopen($filename, 'r');

        $logger->info(sprintf('Uploading %s to: %s', $filename, $this->getName()));

        $this->filesystem->putStream($key, $resource);

        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return new Backup(
            $key,
            $this->filesystem->getSize($key),
            \DateTime::createFromFormat('U', $this->filesystem->getTimestamp($key))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->filesystem->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $backups = array();

        foreach ($this->filesystem->listContents() as $metadata) {
            if ('file' !== $metadata['type']) {
                continue;
            }

            $backups[] = new Backup(
                $metadata['path'],
                $metadata['size'],
                \DateTime::createFromFormat('U', $metadata['timestamp'])
            );
        }

        return $backups;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
