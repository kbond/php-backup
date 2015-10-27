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
    private $scratchDir;
    private $namer;
    private $processor;
    private $sources;
    private $destinations;

    /**
     * @param string        $scratchDir
     * @param Processor     $processor
     * @param Namer         $namer
     * @param Source[]      $sources
     * @param Destination[] $destinations
     */
    public function __construct($scratchDir, Processor $processor, Namer $namer, array $sources, array $destinations)
    {
        $this->scratchDir = $scratchDir;
        $this->processor = $processor;
        $this->namer = $namer;
        $this->sources = $sources;
        $this->destinations = $destinations;
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
}
