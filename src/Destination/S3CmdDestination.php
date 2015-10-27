<?php

namespace Zenstruck\Backup\Destination;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Zenstruck\Backup\Destination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class S3CmdDestination implements Destination
{
    const DEFAULT_TIMEOUT = 300;

    private $bucket;
    private $timeout;
    private $options;

    /**
     * @param string $bucket
     * @param int    $timeout The process timeout in seconds
     * @param array  $options s3cmd command options
     */
    public function __construct($bucket, $timeout = self::DEFAULT_TIMEOUT, array $options = array())
    {
        $this->bucket = $bucket;
        $this->timeout = $timeout;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function push($filename, LoggerInterface $logger)
    {
        $destination = sprintf('%s/%s', $this->bucket, basename($filename));

        $logger->info(sprintf('Uploading %s to: %s', $filename, $destination));

        $process = ProcessBuilder::create($this->options)
            ->setPrefix(array('s3cmd', 'put'))
            ->add($filename)
            ->add($destination)
            ->setTimeout($this->timeout)
            ->getProcess();

        $process->run(
            function ($type, $buffer) use ($logger) {
                $logger->debug($buffer);
            }
        );

        if (!$process->isSuccessful() || false !== strpos($process->getErrorOutput(), 'ERROR:')) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 's3cmd';
    }
}
