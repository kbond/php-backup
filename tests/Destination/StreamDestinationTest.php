<?php

namespace Zenstruck\Backup\Tests\Destination;

use Psr\Log\NullLogger;
use Zenstruck\Backup\Destination\StreamDestination;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class StreamDestinationTest extends TestCase
{
    /**
     * @test
     */
    public function it_copies_file()
    {
        $file = $this->getFixtureDir().'/foo.txt';
        $destinationDir = $this->getScratchDir();
        $destinationFile = $destinationDir.'/foo.txt';
        $destination = new StreamDestination('stream', $destinationDir);
        $this->assertFileNotExists($destinationFile);

        $destination->push($file, new NullLogger());
        $this->assertFileExists($destinationFile);
    }

    /**
     * @test
     */
    public function it_can_get_name()
    {
        $destination = new StreamDestination('stream', 'foo');

        $this->assertSame('stream', $destination->getName());
    }
}
