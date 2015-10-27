<?php

namespace Zenstruck\Backup\Processor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class GzipArchiveProcessor extends ArchiveProcessor
{
    const DEFAULT_OPTIONS = '-czvf';

    public function __construct($options = self::DEFAULT_OPTIONS)
    {
        parent::__construct('tar', $options, 'tar.gz');
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'gzip';
    }
}
