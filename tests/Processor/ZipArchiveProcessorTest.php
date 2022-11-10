<?php

namespace Zenstruck\Backup\Tests\Processor;

use Zenstruck\Backup\Processor\ZipArchiveProcessor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZipArchiveProcessorTest extends ArchiveProcessorTest
{
    protected function getProcessor(): \Zenstruck\Backup\Processor\ArchiveProcessor|ZipArchiveProcessor
    {
        return new ZipArchiveProcessor('archive');
    }

    protected function getExtension(): string
    {
        return 'zip';
    }
}
