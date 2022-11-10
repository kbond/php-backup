<?php

namespace Zenstruck\Backup\Source;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Zenstruck\Backup\Source;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MySqlDumpSource implements Source
{
    public const DEFAULT_USER = 'root';
    public const DEFAULT_SSH_PORT = 22;
    public const DEFAULT_TIMEOUT = 300;

    public function __construct(
        private string $name,
        private string $database,
        private ?string $host = null,
        private string $user = self::DEFAULT_USER,
        private ?string $password = null,
        private ?string $sshHost = null,
        private ?string $sshUser = null,
        private int $sshPort = self::DEFAULT_SSH_PORT,
        private int $timeout = self::DEFAULT_TIMEOUT
    ) {
    }

    public function fetch(string $scratchDir, LoggerInterface $logger)
    {
        $logger->info(\sprintf('Running mysqldump for: %s', $this->database));

        $args = [];

        if (null !== $this->sshHost && null !== $this->sshUser) {
            $args[] = 'ssh';
            $args[] = \sprintf('%s@%s', $this->sshUser, $this->sshHost);
            $args[] = \sprintf('-p %s', $this->sshPort);
        }

        $args[] = 'mysqldump';
        $args[] = \sprintf('-u%s', $this->user);

        if (null !== $this->host) {
            $args[] = \sprintf('-h%s', $this->host);
        }

        if (null !== $this->password) {
            $args[] = \sprintf('-p%s', $this->password);
        }

        $args[] = $this->database;

        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        \file_put_contents(\sprintf('%s/%s.sql', $scratchDir, $this->database), $process->getOutput());
    }

    public function getName(): string
    {
        return $this->name;
    }
}
