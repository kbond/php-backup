<?php

namespace Zenstruck\Backup\Tests\Console\Command;

use Zenstruck\Backup\Console\Command\RunCommand;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RunCommandTest extends ProfileActionCommandTest
{
    /**
     * @test
     */
    public function it_can_execute()
    {
        $commandTester = $this->createCommandTester(array($this->createNullProfile('foo')), 4);
        $commandTester->execute(
            array('command' => $this->getCommandName(), 'profile' => 'foo')
        );
    }

    /**
     * @test
     */
    public function it_can_execute_with_clear()
    {
        $commandTester = $this->createCommandTester(array($this->createNullProfile('foo')), 5);
        $commandTester->execute(
            array('command' => $this->getCommandName(), 'profile' => 'foo', '--clear' => true)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName()
    {
        return 'zenstruck:backup:run';
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommand()
    {
        return new RunCommand();
    }
}
