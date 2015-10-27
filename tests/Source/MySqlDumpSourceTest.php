<?php

namespace Zenstruck\Backup\Tests\Source;

use Psr\Log\NullLogger;
use Zenstruck\Backup\Source\MySqlDumpSource;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MySqlDumpSourceTest extends TestCase
{
    /**
     * @test
     */
    public function it_dumps_a_database()
    {
        $scratch = $this->getScratchDir();
        $file = $scratch.'/zenstruck_backup.sql';

        $source = new MySqlDumpSource('mysqldump', 'zenstruck_backup');
        $this->assertFileNotExists($file);

        $source->fetch($scratch, new NullLogger());
        $this->assertFileExists($file);
        $this->assertContains('Database: zenstruck_backup', file_get_contents($file));
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage mysqldump: Got error: 1049: Unknown database 'foobar' when selecting the database
     */
    public function it_fails_for_invalid_database()
    {
        $scratch = $this->getScratchDir();

        $source = new MySqlDumpSource('mysqldump', 'foobar');
        $source->fetch($scratch, new NullLogger());
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage mysqldump: Got error: 2005: Unknown MySQL server host 'foobar'
     */
    public function it_fails_for_invalid_host()
    {
        $scratch = $this->getScratchDir();

        $source = new MySqlDumpSource('mysqldump', 'zenstruck_backup', 'foobar');
        $source->fetch($scratch, new NullLogger());
    }

    /**
     * @test
     */
    public function it_can_get_name()
    {
        $source = new MySqlDumpSource('mysqldump', 'zenstruck_backup');

        $this->assertSame('mysqldump', $source->getName());
    }
}
