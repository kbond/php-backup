<?php

namespace Zenstruck\Backup;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Executor
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @throws \Exception
     */
    public function backup(Profile $profile, bool $clear = false): BackupCollection
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
     * @return Backup[]
     */
    private function sendToDestinations(Profile $profile, string $filename): array
    {
        $backups = array();

        foreach ($profile->getDestinations() as $destination) {
            $backup = $destination->push($filename, $this->logger);

            $this->logger->info(
                sprintf('Backup created for destination "%s" at: "%s" ', $destination->getName(), $backup->getKey()),
                array(
                    'size' => $backup->getFormattedSize(),
                    'created_at' => $backup->getCreatedAt()->format('Y-m-d H:i:s'),
                )
            );

            $backups[] = $backup;
        }

        return $backups;
    }
}
