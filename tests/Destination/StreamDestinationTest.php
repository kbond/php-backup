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
    public function it_can_get_file()
    {
        $this->prepareScratch();
        $destination = new StreamDestination('stream', $this->getScratchDir());
        $backup = $destination->get('foo.txt');
        $this->assertSame($this->getScratchDir().'/foo.txt', $backup->getKey());
        $this->assertSame(4, $backup->getSize());
        $this->assertInstanceOf('\DateTime', $backup->getCreatedAt());
    }

    /**
     * @test
     */
    public function it_can_delete_a_file()
    {
        $this->prepareScratch();
        $this->assertFileExists($this->getScratchDir().'/foo.txt');

        $destination = new StreamDestination('stream', $this->getScratchDir());
        $destination->delete('foo.txt');
        $this->assertFileNotExists($this->getScratchDir().'/foo.txt');
    }

    /**
     * @test
     */
    public function it_can_list_files()
    {
        $this->prepareScratch();
        $destination = new StreamDestination('stream', $this->getScratchDir());
        $backups = $destination->all();

        $this->assertCount(2, $backups);
        $this->assertInstanceOf('Zenstruck\Backup\Backup', $backups[0]);
        $this->assertInstanceOf('Zenstruck\Backup\Backup', $backups[1]);
    }

    /**
     * @test
     */
    public function it_can_get_name()
    {
        $destination = new StreamDestination('stream', 'foo');

        $this->assertSame('stream', $destination->getName());
    }

    public function prepareScratch()
    {
        $this->filesystem->mirror($this->getFixtureDir(), $this->getScratchDir());
    }
}
