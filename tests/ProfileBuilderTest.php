<?php

namespace Zenstruck\Backup\Tests;

use Zenstruck\Backup\Namer\SimpleNamer;
use Zenstruck\Backup\ProfileBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ProfileBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_create_a_profile()
    {
        $builder = new ProfileBuilder(
            array(new NullProcessor()),
            array(new SimpleNamer()),
            array(new NullSource1(), new NullSource2()),
            array(new NullDestination1(), new NullDestination2())
        );

        $profile = $builder->create(
            'my_profile',
            $this->getScratchDir(),
            'null_processor',
            'backup',
            array('null_source1'),
            array('null_destination1')
        );

        $this->assertSame('my_profile', $profile->getName());
        $this->assertSame($this->getScratchDir(), $profile->getScratchDir());
        $this->assertInstanceOf('Zenstruck\Backup\Tests\NullProcessor', $profile->getProcessor());
        $this->assertInstanceOf('Zenstruck\Backup\Namer\SimpleNamer', $profile->getNamer());
        $this->assertCount(1, $profile->getSources());
        $this->assertCount(1, $profile->getDestinations());

        $source = $profile->getSources();
        $source = $source['null_source1'];
        $this->assertInstanceOf('Zenstruck\Backup\Tests\NullSource1', $source);

        $destination = $profile->getDestinations();
        $destination = $destination['null_destination1'];
        $this->assertInstanceOf('Zenstruck\Backup\Tests\NullDestination1', $destination);
    }

    /**
     * @test
     *
     * @dataProvider invalidItemProvider
     */
    public function it_throws_exceptions_when_asking_for_invalid_items($method, $message)
    {
        $this->setExpectedException('\InvalidArgumentException', $message);

        $builder = new ProfileBuilder();
        $builder->$method('foo');
    }

    public static function invalidItemProvider()
    {
        return array(
            array('getProcessor', 'Processor "foo" is not registered.'),
            array('getNamer', 'Namer "foo" is not registered.'),
            array('getSource', 'Source "foo" is not registered.'),
            array('getDestination', 'Destination "foo" is not registered.'),
        );
    }
}
