<?php

namespace Zenstruck\Backup\Tests\Destination;

use Zenstruck\Backup\Destination\S3CmdDestination;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class S3CmdDestinationTest extends DestinationTest
{
    /**
     * @test
     */
    public function it_pushes_file()
    {
        $this->markTestIncomplete('Test not available for S3CmdDestination');
    }

    /**
     * @test
     */
    public function it_can_get_file()
    {
        $this->markTestIncomplete('Test not available for S3CmdDestination');
    }

    /**
     * @test
     */
    public function it_can_delete_a_file()
    {
        $this->markTestIncomplete('Test not available for S3CmdDestination');
    }

    /**
     * @test
     */
    public function it_can_list_files()
    {
        $this->markTestIncomplete('Test not available for S3CmdDestination');
    }

    public function createDestination($directory, $name = 'foo')
    {
        return new S3CmdDestination($name, 'n/a');
    }
}
