<?php

namespace Zenstruck\Backup\Tests\Console\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Backup\Console\Command\RunCommand;
use Zenstruck\Backup\Console\Helper\BackupHelper;
use Zenstruck\Backup\Executor;
use Zenstruck\Backup\Profile;
use Zenstruck\Backup\ProfileRegistry;
use Zenstruck\Backup\Tests\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RunCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_list_profiles()
    {
        $commandTester = $this->createCommandTester(array($this->createNullProfile('foo')));
        $commandTester->execute(
            array('command' => 'zenstruck:backup:run')
        );

        $this->assertContains(
            'foo  | null_processor | backup | null_source1, null_source2 | null_destination1, null_destination2',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function it_can_execute()
    {
        $commandTester = $this->createCommandTester(array($this->createNullProfile('foo')), 2);
        $commandTester->execute(
            array('command' => 'zenstruck:backup:run', 'profile' => 'foo')
        );
    }

    /**
     * @test
     */
    public function it_can_execute_with_clear()
    {
        $commandTester = $this->createCommandTester(array($this->createNullProfile('foo')), 3);
        $commandTester->execute(
            array('command' => 'zenstruck:backup:run', 'profile' => 'foo', '--clear' => true)
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
            array('command' => 'zenstruck:backup:run', 'profile' => 'foo')
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
    private function createCommandTester(array $profiles = array(), $infoCalls = 0)
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger
            ->expects($this->exactly($infoCalls))
            ->method('info');

        $application = new Application();
        $application->add(new RunCommand());
        $application->getHelperSet()->set(new BackupHelper(new ProfileRegistry($profiles), new Executor($logger)));

        $command = $application->find('zenstruck:backup:run');

        return new CommandTester($command);
    }
}
