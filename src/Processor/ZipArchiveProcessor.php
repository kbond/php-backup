<?php

namespace Zenstruck\Backup\Processor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZipArchiveProcessor extends ArchiveProcessor
{
    const DEFAULT_OPTIONS = '-r';

    public function __construct($name, $options = self::DEFAULT_OPTIONS, $timeout = self::DEFAULT_TIMEOUT)
    {
        parent::__construct($name, 'zip', $options, 'zip', $timeout);
    }
}
