<?php

namespace Zenstruck\Backup\Tests\Namer;

use Zenstruck\Backup\Namer\TimestampNamer;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TimestampNamerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider nameProvider
     */
    public function it_generates_a_name($format, $prefix, $timezone = null)
    {
        $namer = new TimestampNamer('timestamp', $format, $prefix, $timezone);
        $timezone = $timezone ? new \DateTimeZone($timezone) : null;
        $dateTime = new \DateTime('now', $timezone);
        $this->assertSame($prefix.$dateTime->format($format), $namer->generate());
    }

    /**
     * @test
     */
    public function it_can_get_name()
    {
        $namer = new TimestampNamer('timestamp');

        $this->assertSame('timestamp', $namer->getName());
    }

    public static function nameProvider()
    {
        return array(
            array(TimestampNamer::DEFAULT_FORMAT, TimestampNamer::DEFAULT_PREFIX),
            array(TimestampNamer::DEFAULT_FORMAT, TimestampNamer::DEFAULT_PREFIX, 'UTC'),
            array('d', null),
            array('dm', null),
            array('s', 'foo-'),
        );
    }
}
