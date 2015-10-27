<?php

namespace Zenstruck\Backup\Tests\Destination;

use Zenstruck\Backup\Destination\S3CmdDestination;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class S3CmdDestinationTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_name()
    {
        $destination = new S3CmdDestination('s3', 'foo');

        $this->assertSame('s3', $destination->getName());
    }
}
