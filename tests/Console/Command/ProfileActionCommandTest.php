<?php

namespace Zenstruck\Backup\Tests\Console\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Backup\Console\Command\ProfileActionCommand;
use Zenstruck\Backup\Console\Helper\BackupHelper;
use Zenstruck\Backup\Executor;
use Zenstruck\Backup\Profile;
use Zenstruck\Backup\ProfileRegistry;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class ProfileActionCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_list_profiles()
    {
        $commandTester = $this->createCommandTester(array($this->createNullProfile('foo')));
        $commandTester->execute(
            array('command' => $this->getCommandName())
        );

        $this->assertContains(
            'foo  | null_processor | backup | null_source1, null_source2 | null_destination1, null_destination2',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Profile "foo" is not registered.
     */
    public function it_fails_when_asking_for_undefined_profile()
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(
            array('command' => $this->getCommandName(), 'profile' => 'foo')
        );
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No profiles configured.
     */
    public function it_fails_when_listing_no_profiles()
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(
            array('command' => 'zenstruck:backup:run')
        );
    }

    /**
     * @param Profile[] $profiles
     * @param null|int  $infoCalls
     *
     * @return CommandTester
     */
    protected function createCommandTester(array $profiles = array(), $infoCalls = 0)
    {
        $logger = $this->createMock('Psr\Log\LoggerInterface');
        $logger
            ->expects($this->exactly($infoCalls))
            ->method('info');

        $application = new Application();
        $application->add($this->createCommand());
        $application->getHelperSet()->set(new BackupHelper(new ProfileRegistry($profiles), new Executor($logger)));

        $command = $application->find($this->getCommandName());

        return new CommandTester($command);
    }

    /**
     * @return ProfileActionCommand
     */
    abstract protected function createCommand();

    /**
     * @return string
     */
    abstract protected function getCommandName();
}
