<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Namer
{
    /**
     * @return string
     */
    public function generate();

    /**
     * @return string
     */
    public function getName();
}
