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
        $commandTester = $this->createCommandTester([$this->createNullProfile('foo')]);
        $commandTester->execute(
            ['command' => $this->getCommandName()]
        );

        $this->assertStringContainsString(
            'foo  | null_processor | backup | null_source1, null_source2 | null_destination1, null_destination2',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function it_fails_when_asking_for_undefined_profile()
    {
        $this->expectExceptionMessage('Profile "foo" is not registered.');
        $this->expectException(\InvalidArgumentException::class);
        $commandTester = $this->createCommandTester();
        $commandTester->execute(
            ['command' => $this->getCommandName(), 'profile' => 'foo']
        );
    }

    /**
     * @test
     */
    public function it_fails_when_listing_no_profiles()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No profiles configured.');
        $commandTester = $this->createCommandTester();
        $commandTester->execute(
            ['command' => 'zenstruck:backup:run']
        );
    }

    /**
     * @param Profile[] $profiles
     */
    protected function createCommandTester(array $profiles = [], ?int $infoCalls = 0): CommandTester
    {
        $logger = $this->createMock('Psr\Log\LoggerInterface');
        $logger
            ->expects($this->exactly($infoCalls))
            ->method('info')
        ;

        $application = new Application();
        $application->add($this->createCommand());
        $application->getHelperSet()->set(new BackupHelper(new ProfileRegistry($profiles), new Executor($logger)));

        $command = $application->find($this->getCommandName());

        return new CommandTester($command);
    }

    abstract protected function createCommand(): ProfileActionCommand;

    abstract protected function getCommandName(): string;
}
