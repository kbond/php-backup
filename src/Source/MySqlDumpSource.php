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
    const DEFAULT_USER = 'root';
    const DEFAULT_SSH_PORT = 22;
    const DEFAULT_TIMEOUT = 300;

    private $name;
    private $database;
    private $host;
    private $user;
    private $password;
    private $sshHost;
    private $sshUser;
    private $sshPort;
    private $timeout;

    /**
     * @param string      $name
     * @param string      $database
     * @param string|null $host
     * @param string      $user
     * @param string|null $password
     * @param string|null $sshHost
     * @param string|null $sshUser
     * @param int         $sshPort
     * @param int         $timeout
     */
    public function __construct($name, $database, $host = null, $user = self::DEFAULT_USER, $password = null, $sshHost = null, $sshUser = null, $sshPort = self::DEFAULT_SSH_PORT, $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->name = $name;
        $this->database = $database;
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->sshHost = $sshHost;
        $this->sshUser = $sshUser;
        $this->sshPort = $sshPort;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($scratchDir, LoggerInterface $logger)
    {
        $logger->info(sprintf('Running mysqldump for: %s', $this->database));

        $args = [];

        if (null !== $this->sshHost && null !== $this->sshUser) {
            $args[] = 'ssh';
            $args[] = sprintf('%s@%s', $this->sshUser, $this->sshHost);
            $args[] = sprintf('-p %s', $this->sshPort);
        }

        $args[] = 'mysqldump';
        $args[] = sprintf('-u%s', $this->user);

        if (null !== $this->host) {
            $args[] = sprintf('-h%s', $this->host);
        }

        if (null !== $this->password) {
            $args[] = sprintf('-p%s', $this->password);
        }

        $args[] = $this->database;

        $process = new Process($args, null, null, null, $this->timeout);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        file_put_contents(sprintf('%s/%s.sql', $scratchDir, $this->database), $process->getOutput());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
