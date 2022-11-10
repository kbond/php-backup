<?php

namespace Zenstruck\Backup\Processor;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Zenstruck\Backup\Namer;
use Zenstruck\Backup\Processor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class ArchiveProcessor implements Processor
{
    public const DEFAULT_TIMEOUT = 300;

    public function __construct(private string $name, private string $command, private string $options, private string $extension, private int $timeout = self::DEFAULT_TIMEOUT)
    {
    }

    public function process(string $scratchDir, Namer $namer, LoggerInterface $logger): string
    {
        $filename = \sprintf('%s/%s.%s', \sys_get_temp_dir(), $namer->generate(), $this->extension);

        $logger->info(\sprintf('Archiving files to: %s', $filename));

        $process = new Process([$this->command, $this->options, $filename, './'], $scratchDir, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $filename;
    }

    public function cleanup(string $filename, LoggerInterface $logger)
    {
        $logger->info(\sprintf('Deleting %s', $filename));

        if (\file_exists($filename)) {
            \unlink($filename);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
