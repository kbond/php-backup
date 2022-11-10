<?php

namespace Zenstruck\Backup\Tests\Destination;

use Zenstruck\Backup\Destination\StreamDestination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class StreamDestinationTest extends DestinationTest
{
    protected function createDestination(string $directory, string $name = 'foo'): StreamDestination
    {
        return new StreamDestination($name, $directory);
    }
}
