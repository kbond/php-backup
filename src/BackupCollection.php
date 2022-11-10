<?php

namespace Zenstruck\Backup;

use Traversable;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class BackupCollection implements \IteratorAggregate, \Countable
{
    /** @var Backup[] */
    private array $backups;

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

    public function get(int $key): Backup
    {
        return $this->backups[$key];
    }

    /**
     * @return Backup[]
     */
    public function all(): array
    {
        return $this->backups;
    }

    public function getTotalFileSize(): int
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
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->backups);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->backups);
    }
}
