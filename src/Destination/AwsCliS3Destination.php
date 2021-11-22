<?php

namespace Zenstruck\Backup\Destination;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\Destination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AwsCliS3Destination implements Destination
{
    const DEFAULT_TIMEOUT = 300;

    private $name;
    private $bucket;
    private $timeout;
    private $options;

    /**
     * @param string $bucket
     * @param int    $timeout The process timeout in seconds
     * @param array  $options s3cmd command options
     */
    public function __construct($name, $bucket, $timeout = self::DEFAULT_TIMEOUT, array $options = array())
    {
        $this->name = $name;
        $this->bucket = $bucket;
        $this->timeout = $timeout;
        $this->options = $options;
    }

    public function push($filename, LoggerInterface $logger)
    {
        $destination = $this->createPath($filename);

        $logger->info(sprintf('Uploading %s to: %s', $filename, $destination));

        $args = array_merge(['aws', 's3', 'cp'], $this->options, [$filename, $destination]);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful() || false !== strpos($process->getErrorOutput(), 'ERROR:')) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $this->get($filename);
    }

    public function get($key)
    {
        $destination = $this->createPath($key);

        $args = array_merge(['aws', 's3', 'ls'], $this->options, [$destination]);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $backup = $this->parseS3CmdListOutput($process->getOutput());

        if (!isset($backup[0])) {
            throw new \RuntimeException('Error retrieving backup info.');
        }

        return $backup[0];
    }

    public function delete($key)
    {
        throw new \BadMethodCallException(sprintf('%s::%s not yet implemented.', __CLASS__, __METHOD__));
    }

    public function all()
    {
        $args = array_merge(['aws', 's3', 'ls'], $this->options, [trim($this->bucket, '/').'/']);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return new BackupCollection($this->parseS3CmdListOutput($process->getOutput()));
    }

    public function getName()
    {
        return $this->name;
    }

    private function createPath($key)
    {
        return sprintf('%s/%s', $this->bucket, basename($key));
    }

    /**
     * @param string $output
     *
     * @return Backup[]
     */
    private function parseS3CmdListOutput($output)
    {
        $backups = array();

        if (null === $output) {
            return $backups;
        }

        foreach (explode("\n", $output) as $row) {
            if ('' === $row) {
                continue;
            }

            $backup = $this->parseS3CmdListRow($row);

            if (!$backup->getSize()) {
                // first item is the directory, so exclude
                continue;
            }

            $backups[] = $this->parseS3CmdListRow($row);
        }

        return $backups;
    }

    /**
     * @param string
     *
     * @return Backup
     */
    private function parseS3CmdListRow($row)
    {
        $columns = explode(' ', preg_replace('/\s+/', ' ', $row));

        if (4 !== count($columns)) {
            throw new \RuntimeException(sprintf('Error processing result: %s', $row));
        }

        return new Backup($this->createPath($columns[3]), $columns[2], new \DateTime(sprintf('%s %s', $columns[0], $columns[1])));
    }
}
