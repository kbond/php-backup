<?php

namespace Zenstruck\Backup\Destination;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
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
    private Filesystem $filesystem;

    public function __construct(string $name, Filesystem $filesystem)
    {
        $this->name = $name;
        $this->filesystem = $filesystem;
    }

    /**
     * @throws FilesystemException
     */
    public function push(string $filename, LoggerInterface $logger): Backup
    {
        $key = \basename($filename);
        $resource = \fopen($filename, 'r');

        $logger->info(\sprintf('Uploading %s to: %s', $filename, $this->getName()));

        $this->filesystem->writeStream($key, $resource);

        return $this->get($key);
    }

    /**
     * @throws FilesystemException
     */
    public function get(string $key): Backup
    {
        return new Backup(
            $key,
            $this->filesystem->fileSize($key),
            \DateTime::createFromFormat('U', $this->filesystem->lastModified($key))
        );
    }

    /**
     * @throws FilesystemException
     */
    public function delete($key)
    {
        $this->filesystem->delete($key);
    }

    /**
     * @throws FilesystemException
     */
    public function all(): BackupCollection
    {
        $backups = [];

        foreach ($this->filesystem->listContents('') as $metadata) {
            if ('file' !== $metadata['type']) {
                continue;
            }

            $backups[] = new Backup(
                $metadata['path'],
                $metadata['fileSize'],
                \DateTime::createFromFormat('U', $metadata['lastModified'])
            );
        }

        return new BackupCollection($backups);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
