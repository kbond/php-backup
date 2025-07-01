<?php

namespace Zenstruck\Backup\Tests\Destination;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Zenstruck\Backup\Destination\FlysystemDestination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FlysystemDestinationTest extends DestinationTest
{
    protected function setUp(): void
    {
        if (!\class_exists('League\Flysystem\Filesystem')) {
            $this->markTestSkipped('Flysystem not available.');
        }

        parent::setUp();
    }

    protected function createDestination(string $directory, string $name = 'foo'): FlysystemDestination
    {
        return new FlysystemDestination($name, new Filesystem(new LocalFilesystemAdapter($directory)));
    }
}
