<?php

namespace Zenstruck\Backup\Tests\Console\Command;

use Zenstruck\Backup\Console\Command\ListCommand;
use Zenstruck\Backup\Destination\StreamDestination;
use Zenstruck\Backup\Namer\SimpleNamer;
use Zenstruck\Backup\Profile;
use Zenstruck\Backup\Tests\NullProcessor;
use Zenstruck\Backup\Tests\NullSource1;
use Zenstruck\Backup\Tests\NullSource2;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ListCommandTest extends ProfileActionCommandTest
{
    /**
     * @test
     */
    public function it_lists_backups_for_profile()
    {
        $profile = new Profile(
            'foo',
            $this->getScratchDir(),
            new NullProcessor(),
            new SimpleNamer(),
            array(new NullSource1(), new NullSource2()),
            array(new StreamDestination('bar', $this->getFixtureDir()))
        );

        $commandTester = $this->createCommandTester(array($profile));
        $commandTester->execute(
            array('command' => $this->getCommandName(), 'profile' => 'foo')
        );

        $this->assertStringContainsString('foo.txt | 4', $commandTester->getDisplay());
        $this->assertStringContainsString('bam.txt | 4', $commandTester->getDisplay());
        $this->assertStringNotContainsString('baz.txt | 4', $commandTester->getDisplay());
    }

    /**
     * {@inheritdoc}
     */
    protected function createCommand(): ListCommand|\Zenstruck\Backup\Console\Command\ProfileActionCommand
    {
        return new ListCommand();
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName(): string
    {
        return 'zenstruck:backup:list';
    }
}
