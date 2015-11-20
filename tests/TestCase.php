<?php

namespace Zenstruck\Backup\Tests;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Zenstruck\Backup\Backup;
use Zenstruck\Backup\BackupCollection;
use Zenstruck\Backup\Destination;
use Zenstruck\Backup\Namer;
use Zenstruck\Backup\Namer\SimpleNamer;
use Zenstruck\Backup\Processor;
use Zenstruck\Backup\Profile;
use Zenstruck\Backup\Source;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    protected $filesystem;

    /** @var BackupCollection */
    protected $collection;

    protected function setUp()
    {
        $this->filesystem = new Filesystem();

        $this->removeScratchDir();

        $this->filesystem->mkdir($this->getScratchDir());

        $this->collection = new BackupCollection(array(
            new Backup('today', 5, new \DateTime('today')),
            new Backup('tomorrow', 9, new \DateTime('tomorrow')),
            new Backup('yesterday', 22, new \DateTime('yesterday')),
        ));
    }

    protected function tearDown()
    {
        $this->removeScratchDir();
    }

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures';
    }

    protected function getScratchDir()
    {
        return sys_get_temp_dir().'/zenstruck-backup-bundle';
    }

    protected function createNullProfile($name = 'null_profile')
    {
        return new Profile(
            $name,
            $this->getScratchDir(),
            new NullProcessor(),
            new SimpleNamer(),
            array(new NullSource1(), new NullSource2()),
            array(new NullDestination1(), new NullDestination2())
        );
    }

    private function removeScratchDir()
    {
        if (is_dir($dir = $this->getScratchDir())) {
            $this->filesystem->remove($dir);
        }
    }
}

class NullProcessor implements Processor
{
    public function getName()
    {
        return 'null_processor';
    }

    public function process($scratchDir, Namer $namer, LoggerInterface $logger)
    {
        // noop
    }

    public function cleanup($filename, LoggerInterface $logger)
    {
        // noop
    }
}

class NullSource1 implements Source
{
    public function getName()
    {
        return 'null_source1';
    }

    public function fetch($scratchDir, LoggerInterface $logger)
    {
        // noop
    }
}

class NullSource2 extends NullSource1
{
    public function getName()
    {
        return 'null_source2';
    }
}

class NullDestination1 implements Destination
{
    public function push($filename, LoggerInterface $logger)
    {
        return new Backup('null', 0, new \DateTime());
    }

    public function get($key)
    {
        // noop
    }

    public function delete($key)
    {
        // noop
    }

    public function all()
    {
        // noop
    }

    public function getName()
    {
        return 'null_destination1';
    }
}

class NullDestination2 extends NullDestination1
{
    public function getName()
    {
        return 'null_destination2';
    }
}
