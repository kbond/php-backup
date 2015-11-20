<?php

namespace Zenstruck\Backup\Source;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Zenstruck\Backup\Source;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RsyncSource implements Source
{
    const DEFAULT_TIMEOUT = 300;

    private $name;
    private $source;
    private $options;
    private $timeout;

    /**
     * @param string $name
     * @param string $source            The rsync source
     * @param array  $additionalOptions Additional rsync options (useful for excludes)
     * @param array  $defaultOptions    Default rsync options
     * @param int    $timeout
     */
    public function __construct($name, $source, array $additionalOptions = array(), array $defaultOptions = array(), $timeout = self::DEFAULT_TIMEOUT)
    {
        $defaultOptions = count($defaultOptions) ? $defaultOptions : static::getDefaultOptions();

        $this->name = $name;
        $this->source = $source;
        $this->options = $defaultOptions;
        $this->timeout = $timeout;

        foreach ($additionalOptions as $option) {
            $this->options[] = $option;
        }
    }

    public static function getDefaultOptions()
    {
        return array('-acrv', '--force', '--delete', '--progress', '--delete-excluded');
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($scratchDir, LoggerInterface $logger)
    {
        $logger->info(sprintf('Syncing files from: %s', $this->source));

        $process = ProcessBuilder::create($this->options)
            ->setTimeout($this->timeout)
            ->setPrefix('rsync')
            ->add($this->source)
            ->add($scratchDir)
            ->getProcess();

        $process->run(
            function ($type, $buffer) use ($logger) {
                $logger->debug($buffer);
            }
        );

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
