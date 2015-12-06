<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class BackupCollection implements \IteratorAggregate, \Countable
{
    /** @var Backup[] */
    private $backups;

    /**
     * @param Backup[] $backups
     */
    public function __construct(array $backups = array())
    {
        usort($backups, function (Backup $a, Backup $b) {
            $timestampA = $a->getCreatedAt()->getTimestamp();
            $timestampB = $b->getCreatedAt()->getTimestamp();

            if ($timestampA === $timestampB) {
                return 0;
            }

            return ($timestampA < $timestampB) ? -1 : 1;
        });

        $this->backups = array_values($backups);
    }

    /**
     * @param int $key
     *
     * @return Backup
     */
    public function get($key)
    {
        return $this->backups[$key];
    }

    /**
     * @return Backup[]
     */
    public function all()
    {
        return $this->backups;
    }

    /**
     * @return int
     */
    public function getTotalFileSize()
    {
        $size = 0;

        foreach ($this->backups as $backup) {
            $size += $backup->getSize();
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->backups);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->backups);
    }
}
