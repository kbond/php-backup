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
    protected function getProcessor(): GzipArchiveProcessor|\Zenstruck\Backup\Processor\ArchiveProcessor
    {
        return new GzipArchiveProcessor('archive');
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension(): string
    {
        return 'tar.gz';
    }
}
