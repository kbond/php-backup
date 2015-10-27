<?php

namespace Zenstruck\Backup\Processor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class GzipArchiveProcessor extends ArchiveProcessor
{
    const DEFAULT_OPTIONS = '-czvf';

    public function __construct($name, $options = self::DEFAULT_OPTIONS)
    {
        parent::__construct($name, 'tar', $options, 'tar.gz');
    }
}
