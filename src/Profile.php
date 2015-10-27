<?php

namespace Zenstruck\Backup;

use Zenstruck\Backup\Destination;
use Zenstruck\Backup\Namer;
use Zenstruck\Backup\Processor;
use Zenstruck\Backup\Source;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Profile
{
    private $name;
    private $scratchDir;
    private $namer;
    private $processor;
    private $sources;
    private $destinations;

    /**
     * @param string        $name
     * @param string        $scratchDir
     * @param Processor     $processor
     * @param Namer         $namer
     * @param Source[]      $sources
     * @param Destination[] $destinations
     */
    public function __construct($name, $scratchDir, Processor $processor, Namer $namer, array $sources, array $destinations)
    {
        $this->name = $name;
        $this->scratchDir = $scratchDir;
        $this->processor = $processor;
        $this->namer = $namer;

        foreach ($sources as $source) {
            $this->addSource($source);
        }

        foreach ($destinations as $destination) {
            $this->addDestination($destination);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getScratchDir()
    {
        return $this->scratchDir;
    }

    /**
     * @return Namer
     */
    public function getNamer()
    {
        return $this->namer;
    }

    /**
     * @return Processor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * @return Source[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @return Destination[]
     */
    public function getDestinations()
    {
        return $this->destinations;
    }

    private function addSource(Source $source)
    {
        $this->sources[$source->getName()] = $source;
    }

    private function addDestination(Destination $destination)
    {
        $this->destinations[$destination->getName()] = $destination;
    }
}
