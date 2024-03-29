<?php

namespace Zenstruck\Backup\Processor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class GzipArchiveProcessor extends ArchiveProcessor
{
    public const DEFAULT_OPTIONS = '-czvf';

    public function __construct($name, $options = self::DEFAULT_OPTIONS, $timeout = self::DEFAULT_TIMEOUT)
    {
        parent::__construct($name, 'tar', $options, 'tar.gz', $timeout);
    }
}
