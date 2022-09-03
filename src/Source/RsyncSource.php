<?php

namespace Zenstruck\Backup\Source;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Zenstruck\Backup\Source;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RsyncSource implements Source
{
    const DEFAULT_TIMEOUT = 300;

    private array $options;

    /**
     * @param string $source            The rsync source
     * @param array  $additionalOptions Additional rsync options (useful for excludes)
     * @param array  $defaultOptions    Default rsync options
     */
    public function __construct(private string $name,
                                private string $source,
                                array          $additionalOptions = [],
                                array          $defaultOptions = [],
                                private int    $timeout = self::DEFAULT_TIMEOUT)
    {
        $defaultOptions = count($defaultOptions) ? $defaultOptions : static::getDefaultOptions();

        $this->options = $defaultOptions;

        foreach ($additionalOptions as $option) {
            $this->options[] = $option;
        }
    }

    public static function getDefaultOptions(): array
    {
        return array('-acrv', '--force', '--delete', '--progress', '--delete-excluded');
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(string $scratchDir, LoggerInterface $logger)
    {
        $logger->info(sprintf('Syncing files from: %s', $this->source));

        $args = array_merge(['rsync'], $this->options, [$this->source, $scratchDir]);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
