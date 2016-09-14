<?php

namespace Zenstruck\Backup\Destination;

use Psr\Log\LoggerInterface;
use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\Destination;
use Zenstruck\Backup\Namer\TimestampNamer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CreatedAtFromFilenameDestination implements Destination
{
    private $destination;
    private $timestampNamer;

    public function __construct(Destination $destination, TimestampNamer $timestampNamer)
    {
        $this->destination = $destination;
        $this->timestampNamer = $timestampNamer;
    }

    /**
     * {@inheritdoc}
     */
    public function push($filename, LoggerInterface $logger)
    {
        return $this->convert($this->destination->push($filename, $logger));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->convert($this->destination->get($key));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->destination->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return new BackupCollection(
            array_map(array($this, 'convert'), iterator_to_array($this->destination->all()))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->destination->getName();
    }

    /**
     * @param Backup $backup
     *
     * @return Backup
     */
    private function convert(Backup $backup)
    {
        $timestamp = str_replace($this->timestampNamer->getPrefix(), '', $backup->getKey());
        $createdAt = \DateTime::createFromFormat($this->timestampNamer->getFormat(), $timestamp);

        if (!$createdAt instanceof \DateTime) {
            return $backup;
        }

        return new Backup($backup->getKey(), $backup->getSize(), $createdAt);
    }
}
