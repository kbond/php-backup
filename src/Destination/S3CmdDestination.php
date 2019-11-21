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
class S3CmdDestination implements Destination
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

    /**
     * {@inheritdoc}
     */
    public function push($filename, LoggerInterface $logger)
    {
        $destination = $this->createPath($filename);

        $logger->info(sprintf('Uploading %s to: %s', $filename, $destination));

        $args = array_merge(['s3cmd', 'put'], $this->options, [$filename, $destination]);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful() || false !== strpos($process->getErrorOutput(), 'ERROR:')) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $this->get($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $destination = $this->createPath($key);

        $args = array_merge(['s3cmd', 'info'], $this->options, [$destination]);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $output = $process->getOutput();

        preg_match('#File size\:\s+(\d+)\s+Last mod\:\s+(.+)#', $output, $matches);

        if (3 !== count($matches)) {
            throw new \RuntimeException(sprintf('Error processing result: %s', $output));
        }

        return new Backup($destination, $matches[1], new \DateTime($matches[2]));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        throw new \BadMethodCallException(sprintf('%s::%s not yet implemented.', __CLASS__, __METHOD__));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $args = array_merge(['s3cmd', 'ls'], $this->options, [trim($this->bucket, '/').'/']);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return new BackupCollection($this->parseS3CmdListOutput($process->getOutput()));
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

        return new Backup($columns[3], $columns[2], new \DateTime(sprintf('%s %s', $columns[0], $columns[1])));
    }
}
