<?php

namespace Zenstruck\Backup\Tests\Processor;

use Zenstruck\Backup\Processor\ZipArchiveProcessor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZipArchiveProcessorTest extends ArchiveProcessorTest
{
    /**
     * {@inheritdoc}
     */
    protected function getProcessor()
    {
        return new ZipArchiveProcessor('archive');
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'zip';
    }
}
