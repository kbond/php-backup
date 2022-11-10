<?php

namespace Zenstruck\Backup;

use Symfony\Component\Console\Helper\Helper;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Backup
{
    private string $key;
    private int $size;
    private \DateTime $createdAt;

    /**
     * @param string $path The path to the file.
     */
    public static function fromFile(string $path): Backup
    {
        return new self($path, filesize($path), \DateTime::createFromFormat('U', filemtime($path)));
    }

    public function __construct(string $key, int $size, \DateTime $createdAt)
    {
        $this->key = $key;
        $this->size = $size;
        $this->createdAt = $createdAt;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int The size in bytes.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    public function getFormattedSize(): string
    {
        return \class_exists(Helper::class) ? Helper::formatMemory($this->size) : $this->size.' B';
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
