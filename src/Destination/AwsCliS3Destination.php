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
    public const DEFAULT_TIMEOUT = 300;

    /**
     * @param int   $timeout The process timeout in seconds
     * @param array $options s3cmd command options
     */
    public function __construct(private string $name, private string $bucket, private int $timeout = self::DEFAULT_TIMEOUT, private array $options = [])
    {
    }

    /**
     * @throws \Exception
     */
    public function push(string $filename, LoggerInterface $logger): Backup
    {
        $destination = $this->createPath($filename);

        $logger->info(\sprintf('Uploading %s to: %s', $filename, $destination));

        $args = \array_merge(['aws', 's3', 'cp'], $this->options, [$filename, $destination]);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful() || \str_contains($process->getErrorOutput(), 'ERROR:')) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $this->get($filename);
    }

    /**
     * @throws \Exception
     */
    public function get(string $key): Backup
    {
        $destination = $this->createPath($key);

        $args = \array_merge(['aws', 's3', 'ls'], $this->options, [$destination]);
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
        throw new \BadMethodCallException(\sprintf('%s::%s not yet implemented.', __CLASS__, __METHOD__));
    }

    /**
     * @throws \Exception
     */
    public function all(): BackupCollection
    {
        $args = \array_merge(['aws', 's3', 'ls'], $this->options, [\trim($this->bucket, '/').'/']);
        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return new BackupCollection($this->parseS3CmdListOutput($process->getOutput()));
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function createPath($key): string
    {
        return \sprintf('%s/%s', $this->bucket, \basename($key));
    }

    /**
     * @return Backup[]
     *
     * @throws \Exception
     */
    private function parseS3CmdListOutput(string $output): array
    {
        $backups = [];

        if (null === $output) {
            return $backups;
        }

        foreach (\explode("\n", $output) as $row) {
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
     * @throws \Exception
     */
    private function parseS3CmdListRow(string $row): Backup
    {
        $columns = \explode(' ', \preg_replace('/\s+/', ' ', $row));

        if (4 !== \count($columns)) {
            throw new \RuntimeException(\sprintf('Error processing result: %s', $row));
        }

        return new Backup($this->createPath($columns[3]), $columns[2], new \DateTime(\sprintf('%s %s', $columns[0], $columns[1])));
    }
}
