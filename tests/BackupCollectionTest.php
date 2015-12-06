<?php

namespace Zenstruck\Backup\Tests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class BackupCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function backups_are_ordered_by_time_oldest_to_newest()
    {
        $this->assertSame('yesterday', $this->collection->get(0)->getKey());
        $this->assertSame('today', $this->collection->get(1)->getKey());
        $this->assertSame('tomorrow', $this->collection->get(2)->getKey());
    }

    /**
     * @test
     */
    public function can_get_total_filesize()
    {
        $this->assertSame(36, $this->collection->getTotalFileSize());
    }
}
