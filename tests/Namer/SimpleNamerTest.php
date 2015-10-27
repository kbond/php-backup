<?php

namespace Zenstruck\Backup\Tests\Namer;

use Zenstruck\Backup\Namer\SimpleNamer;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class SimpleNamerTest extends TestCase
{
    /**
     * @test
     */
    public function it_generates_a_name()
    {
        $namer1 = new SimpleNamer();
        $namer2 = new SimpleNamer('foo');
        $this->assertSame(SimpleNamer::DEFAULT_NAME, $namer1->generate());
        $this->assertSame('foo', $namer2->generate());
    }

    /**
     * @test
     */
    public function it_can_get_name()
    {
        $namer = new SimpleNamer();

        $this->assertSame('backup', $namer->getName());
    }
}
