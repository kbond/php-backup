<?php

namespace Zenstruck\Backup\Namer;

use Zenstruck\Backup\Namer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class SimpleNamer implements Namer
{
    const DEFAULT_NAME = 'backup';

    private string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name = self::DEFAULT_NAME)
    {
        $this->name = $name;
    }

    public function generate(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
