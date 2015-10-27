<?php

namespace Zenstruck\Backup\Tests\Processor;

use Zenstruck\Backup\Processor\GzipArchiveProcessor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class GzipArchiveProcessorTest extends ArchiveProcessorTest
{
    /**
     * {@inheritdoc}
     */
    protected function getProcessor()
    {
        return new GzipArchiveProcessor('archive');
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'tar.gz';
    }
}
