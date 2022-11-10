<?php

namespace Zenstruck\Backup\Tests\Source;

use Psr\Log\NullLogger;
use Zenstruck\Backup\Source\RsyncSource;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RsyncSourceTest extends TestCase
{
    /**
     * @test
     */
    public function it_rsyncs_files()
    {
        $scratch = $this->getScratchDir();

        $source = new RsyncSource('rysnc', $this->getFixtureDir());
        $this->assertFileDoesNotExist($scratch.'/Fixtures/foo.txt');
        $this->assertFileDoesNotExist($scratch.'/Fixtures/bar/baz.txt');

        $source->fetch($scratch, new NullLogger());
        $this->assertFileExists($scratch.'/Fixtures/foo.txt');
        $this->assertFileExists($scratch.'/Fixtures/bar/baz.txt');
    }

    /**
     * @test
     */
    public function it_fails_for_invalid_directory()
    {
        $this->expectException(\RuntimeException::class);
        $scratch = $this->getScratchDir();

        $source = new RsyncSource('rsync', '/foo/bar');
        $source->fetch($scratch, new NullLogger());
    }

    /**
     * @test
     */
    public function it_can_get_name()
    {
        $source = new RsyncSource('rsync', '/foo');

        $this->assertSame('rsync', $source->getName());
    }
}
