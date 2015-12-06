<?php

namespace Zenstruck\Backup\Tests\RotateStrategy;

use Zenstruck\Backup\Backup;
use Zenstruck\Backup\RotateStrategy\ChainRotateStrategy;
use Zenstruck\Backup\RotateStrategy\MaxCountRotateStrategy;
use Zenstruck\Backup\RotateStrategy\MaxSizeRotateStrategy;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ChainRotateStrategyTest extends TestCase
{
    /**
     * @test
     */
    public function returns_nothing_if_requirements_not_met()
    {
        $strategy = new ChainRotateStrategy(array(
            new MaxSizeRotateStrategy(100),
            new MaxCountRotateStrategy(10),
        ));
        $toRemove = $strategy->getBackupsToRemove($this->collection, new Backup('foo', 6, new \DateTime()));

        $this->assertCount(0, $toRemove);
    }

    /**
     * @test
     */
    public function returns_backups_to_remove()
    {
        $strategy = new ChainRotateStrategy(array(
            new MaxSizeRotateStrategy(100),
            new MaxCountRotateStrategy(2),
        ));
        $toRemove = $strategy->getBackupsToRemove($this->collection, new Backup('foo', 6, new \DateTime()));

        $this->assertCount(2, $toRemove);
        $this->assertSame('yesterday', $toRemove->get(0)->getKey());
        $this->assertSame('today', $toRemove->get(1)->getKey());
    }
}
