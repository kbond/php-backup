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
    private $name;
    private $directory;
    private $filesystem;

    /**
     * @param string $name
     * @param string $directory
     */
    public function __construct($name, $directory)
    {
        $this->name = $name;
        $this->directory = $directory;
        $this->filesystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function push($filename, LoggerInterface $logger)
    {
        $logger->info(sprintf('Copying %s to %s', $filename, $this->directory));

        $this->filesystem->copy($filename, $this->createPath($filename), true);

        return $this->get($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return Backup::fromFile($this->createPath($key));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->filesystem->remove($this->createPath($key));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $backups = array();

        /** @var SplFileInfo[] $files */
        $files = Finder::create()->in($this->directory)->files()->depth(0)->sortByModifiedTime();

        foreach ($files as $file) {
            $backups[] = Backup::fromFile($file->getPathname());
        }

        return new BackupCollection($backups);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function createPath($key)
    {
        return sprintf('%s/%s', $this->directory, basename($key));
    }
}
