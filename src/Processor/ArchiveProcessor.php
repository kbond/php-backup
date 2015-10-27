<?php

namespace Zenstruck\Backup\Processor;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Zenstruck\Backup\Namer;
use Zenstruck\Backup\Processor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class ArchiveProcessor implements Processor
{
    private $name;
    private $command;
    private $options;
    private $extension;

    /**
     * @param string $name
     * @param string $command
     * @param string $options
     * @param string $extension
     */
    public function __construct($name, $command, $options, $extension)
    {
        $this->name = $name;
        $this->command = $command;
        $this->options = $options;
        $this->extension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function process($scratchDir, Namer $namer, LoggerInterface $logger)
    {
        $filename = sprintf('%s/%s.%s', sys_get_temp_dir(), $namer->generate(), $this->extension);

        $logger->info(sprintf('Archiving files to: %s', $filename));

        $process = ProcessBuilder::create(array($this->command, $this->options, $filename, './'))
            ->setWorkingDirectory($scratchDir)
            ->getProcess();

        $process->run(
            function ($type, $buffer) use ($logger) {
                $logger->debug($buffer);
            }
        );

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanup($filename, LoggerInterface $logger)
    {
        $logger->info(sprintf('Deleting %s', $filename));

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
