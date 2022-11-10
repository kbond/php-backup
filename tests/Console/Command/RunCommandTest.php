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
        $commandTester = $this->createCommandTester([$this->createNullProfile('foo')], 4);
        $commandTester->execute(
            ['command' => $this->getCommandName(), 'profile' => 'foo']
        );
    }

    /**
     * @test
     */
    public function it_can_execute_with_clear()
    {
        $commandTester = $this->createCommandTester([$this->createNullProfile('foo')], 5);
        $commandTester->execute(
            ['command' => $this->getCommandName(), 'profile' => 'foo', '--clear' => true]
        );
    }

    protected function getCommandName(): string
    {
        return 'zenstruck:backup:run';
    }

    protected function createCommand(): RunCommand|\Zenstruck\Backup\Console\Command\ProfileActionCommand
    {
        return new RunCommand();
    }
}
