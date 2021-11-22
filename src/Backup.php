<?php

namespace Zenstruck\Backup;

use Symfony\Component\Console\Helper\Helper;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Backup
{
    private $key;
    private $size;
    private $createdAt;

    /**
     * @param string $path The path to the file.
     *
     * @return Backup
     */
    public static function fromFile($path)
    {
        return new self($path, filesize($path), \DateTime::createFromFormat('U', filemtime($path)));
    }

    /**
     * @param string    $key
     * @param int       $size
     * @param \DateTime $createdAt
     */
    public function __construct($key, $size, \DateTime $createdAt)
    {
        $this->key = $key;
        $this->size = (int) $size;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return int The size in bytes.
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getFormattedSize()
    {
        return \class_exists(Helper::class) ? Helper::formatMemory($this->size) : $this->size.' B';
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
