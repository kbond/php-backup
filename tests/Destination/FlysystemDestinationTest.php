<?php

namespace Zenstruck\Backup\Tests\Destination;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Zenstruck\Backup\Destination\FlysystemDestination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FlysystemDestinationTest extends DestinationTest
{
    public function setUp()
    {
        if (!class_exists('League\Flysystem\Filesystem')) {
            $this->markTestSkipped('Flysystem not available.');
        }

        parent::setUp();
    }

    protected function createDestination($directory, $name = 'foo')
    {
        return new FlysystemDestination($name, new Filesystem(new Local($directory)));
    }
}
