<?php

namespace Zenstruck\Backup\Tests\Destination;

use Zenstruck\Backup\Destination\RotatedDestination;
use Zenstruck\Backup\Destination\StreamDestination;
use Zenstruck\Backup\RotateStrategy\ChainRotateStrategy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RotatedDestinationTest extends DestinationTest
{
    /**
     * @test
     */
    public function can_run_rotation()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->exactly(3))
            ->method('info')
            ->withConsecutive(
                array('Removing backup "yesterday" from destination "my_destination"'),
                array('Removing backup "today" from destination "my_destination"'),
                array('Removing backup "tomorrow" from destination "my_destination"')
            );
        $file = $this->getFixtureDir().'/foo.txt';
        $destination = $this->getMock('Zenstruck\Backup\Destination');
        $destination->expects($this->once())
            ->method('push')
            ->with($file, $logger);
        $destination->expects($this->once())
            ->method('all')
            ->willReturn($this->collection);
        $destination->expects($this->exactly(3))
            ->method('delete')
            ->withConsecutive(array('yesterday'), array('today'), array('tomorrow'));
        $destination->expects($this->exactly(3))
            ->method('getName')
            ->willReturn('my_destination');
        $strategy = $this->getMock('Zenstruck\Backup\RotateStrategy');
        $strategy->expects($this->once())
            ->method('getBackupsToRemove')
            ->with($this->collection, $this->isInstanceOf('Zenstruck\Backup\Backup'))
            ->willReturn($this->collection);

        $destination = new RotatedDestination($destination, $strategy);
        $destination->push($file, $logger);
    }

    protected function createDestination($directory, $name = 'foo')
    {
        return new RotatedDestination(
            new StreamDestination($name, $directory),
            new ChainRotateStrategy(array())
        );
    }
}
