<?php

namespace Zenstruck\Backup;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Executor
{
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Profile $profile
     * @param bool    $clear
     *
     * @return BackupCollection
     *
     * @throws \Exception
     */
    public function backup(Profile $profile, $clear = false)
    {
        $scratchDir = $profile->getScratchDir();
        $processor = $profile->getProcessor();
        $filesystem = new Filesystem();

        if ($clear) {
            $this->logger->info('Clearing scratch directory...');
            $filesystem->remove($scratchDir);
        }

        if (!is_dir($scratchDir)) {
            $filesystem->mkdir($scratchDir);
        }

        $this->logger->info('Beginning backup...');

        foreach ($profile->getSources() as $source) {
            $source->fetch($scratchDir, $this->logger);
        }

        $filename = $processor->process($scratchDir, $profile->getNamer(), $this->logger);

        try {
            $backups = $this->sendToDestinations($profile, $filename);
        } catch (\Exception $e) {
            $processor->cleanup($filename, $this->logger);

            throw $e;
        }

        $processor->cleanup($filename, $this->logger);
        $this->logger->info('Done.');

        return new BackupCollection($backups);
    }

    /**
     * @param Profile $profile
     * @param string  $filename
     *
     * @return Backup[]
     */
    private function sendToDestinations(Profile $profile, $filename)
    {
        $backups = array();

        foreach ($profile->getDestinations() as $destination) {
            $backup = $destination->push($filename, $this->logger);

            $size = $backup->getSize();

            if (class_exists('\\ByteUnits\\System')) {
                $size = \ByteUnits\parse($size)->format('B');
            }

            $this->logger->info(
                sprintf('Backup created for destination "%s" at: "%s" ', $destination->getName(), $backup->getKey()),
                array(
                    'size' => $size,
                    'created_at' => $backup->getCreatedAt()->format('Y-m-d H:i:s'),
                )
            );

            $backups[] = $backup;
        }

        return $backups;
    }
}
