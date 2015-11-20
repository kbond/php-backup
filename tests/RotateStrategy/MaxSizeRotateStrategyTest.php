<?php

namespace Zenstruck\Backup\Tests\RotateStrategy;

use Zenstruck\Backup\Backup;
use Zenstruck\Backup\RotateStrategy\MaxSizeRotateStrategy;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MaxSizeRotateStrategyTest extends TestCase
{
    /**
     * @test
     */
    public function returns_nothing_if_max_size_is_not_met()
    {
        $strategy = new MaxSizeRotateStrategy(100);
        $toRemove = $strategy->getBackupsToRemove($this->collection, new Backup('foo', 6, new \DateTime()));

        $this->assertCount(0, $toRemove);
    }

    /**
     * @test
     */
    public function returns_backups_to_remove()
    {
        $strategy = new MaxSizeRotateStrategy(20);
        $toRemove = $strategy->getBackupsToRemove($this->collection, new Backup('foo', 6, new \DateTime()));

        $this->assertCount(2, $toRemove);
        $this->assertSame('yesterday', $toRemove->get(0)->getKey());
        $this->assertSame('today', $toRemove->get(1)->getKey());
    }
}
