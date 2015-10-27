<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Namer extends HasName
{
    /**
     * @return string
     */
    public function generate();
}
