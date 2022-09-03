<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Namer
{
    public function generate(): string;

    public function getName(): string;
}
