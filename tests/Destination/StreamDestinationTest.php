<?php

namespace Zenstruck\Backup\Tests\Destination;

use Zenstruck\Backup\Destination\StreamDestination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class StreamDestinationTest extends DestinationTest
{
    protected function createDestination($directory, $name = 'foo')
    {
        return new StreamDestination($name, $directory);
    }
}
