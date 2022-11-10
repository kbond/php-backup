<?php

namespace Zenstruck\Backup\Destination;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\Destination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class StreamDestination implements Destination
{
    private string $name;
    private string $directory;
    private Filesystem $filesystem;

    /**
     * @param string $name
     * @param string $directory
     */
    public function __construct(string $name, string $directory)
    {
        $this->name = $name;
        $this->directory = $directory;
        $this->filesystem = new Filesystem();
    }

    public function push(string $filename, LoggerInterface $logger): Backup
    {
        $logger->info(sprintf('Copying %s to %s', $filename, $this->directory));

        $this->filesystem->copy($filename, $this->createPath($filename), true);

        return $this->get($filename);
    }

    public function get(string $key): Backup
    {
        return Backup::fromFile($this->createPath($key));
    }

    public function delete($key)
    {
        $this->filesystem->remove($this->createPath($key));
    }

    public function all(): BackupCollection
    {
        $backups = array();

        /** @var SplFileInfo[] $files */
        $files = Finder::create()->in($this->directory)->files()->depth(0)->sortByModifiedTime();

        foreach ($files as $file) {
            $backups[] = Backup::fromFile($file->getPathname());
        }

        return new BackupCollection($backups);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function createPath(string $key): string
    {
        return sprintf('%s/%s', $this->directory, basename($key));
    }
}
