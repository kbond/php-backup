<?php

namespace Zenstruck\Backup\Tests\Destination;

use Psr\Log\NullLogger;
use Zenstruck\Backup\Destination;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class DestinationTest extends TestCase
{
    /**
     * @test
     */
    public function it_pushes_file()
    {
        $file = $this->getFixtureDir().'/foo.txt';
        $destinationDir = $this->getScratchDir();
        $destinationFile = $destinationDir.'/foo.txt';
        $destination = $this->createDestination($destinationDir);
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
        $destination = $this->createDestination($this->getScratchDir());
        $backup = $destination->get('foo.txt');
        $this->assertContains('foo.txt', $backup->getKey());
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

        $destination = $this->createDestination($this->getScratchDir());
        $destination->delete('foo.txt');
        $this->assertFileNotExists($this->getScratchDir().'/foo.txt');
    }

    /**
     * @test
     */
    public function it_can_list_files()
    {
        $this->prepareScratch();
        $destination = $this->createDestination($this->getScratchDir());
        $backups = $destination->all();

        $this->assertCount(2, $backups);
        $this->assertInstanceOf('Zenstruck\Backup\Backup', $backups->get(1));
        $this->assertInstanceOf('Zenstruck\Backup\Backup', $backups->get(1));
    }

    /**
     * @test
     */
    public function it_can_get_name()
    {
        $destination = $this->createDestination($this->getScratchDir(), 'foo');

        $this->assertSame('foo', $destination->getName());
    }

    /**
     * @param string $directory
     * @param string $name
     *
     * @return Destination
     */
    abstract protected function createDestination($directory, $name = 'foo');

    private function prepareScratch()
    {
        $this->filesystem->mirror($this->getFixtureDir(), $this->getScratchDir());
    }
}
