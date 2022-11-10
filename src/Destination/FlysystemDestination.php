<?php

namespace Zenstruck\Backup\Destination;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\Destination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FlysystemDestination implements Destination
{
    private string $name;
    private FilesystemInterface $filesystem;

    public function __construct(string $name, FilesystemInterface $filesystem)
    {
        $this->name = $name;
        $this->filesystem = $filesystem;
    }

    /**
     * @throws FileNotFoundException
     */
    public function push(string $filename, LoggerInterface $logger): Backup
    {
        $key = \basename($filename);
        $resource = \fopen($filename, 'r');

        $logger->info(\sprintf('Uploading %s to: %s', $filename, $this->getName()));

        $this->filesystem->putStream($key, $resource);

        return $this->get($key);
    }

    /**
     * @throws FileNotFoundException
     */
    public function get(string $key): Backup
    {
        return new Backup(
            $key,
            $this->filesystem->getSize($key),
            \DateTime::createFromFormat('U', $this->filesystem->getTimestamp($key))
        );
    }

    /**
     * @throws FileNotFoundException
     */
    public function delete($key)
    {
        $this->filesystem->delete($key);
    }

    public function all(): BackupCollection
    {
        $backups = [];

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

        return new BackupCollection($backups);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
