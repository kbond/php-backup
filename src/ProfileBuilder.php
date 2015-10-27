<?php

namespace Zenstruck\Backup;

use Zenstruck\Backup\Destination;
use Zenstruck\Backup\Namer;
use Zenstruck\Backup\Processor;
use Zenstruck\Backup\Source;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ProfileBuilder
{
    private $processors;
    private $namers;
    private $sources;
    private $destinations;

    /**
     * @param Processor[]   $processors
     * @param Namer[]       $namers
     * @param Source[]      $sources
     * @param Destination[] $destinations
     */
    public function __construct(array $processors = array(), array $namers = array(), array $sources = array(), array $destinations = array())
    {
        $this->processors = $processors;
        $this->namers = $namers;
        $this->sources = $sources;
        $this->destinations = $destinations;
    }

    /**
     * @param string $scratchDir
     * @param string $processor
     * @param string $namer
     * @param array  $sources
     * @param array  $destinations
     *
     * @return Profile
     */
    public function create($scratchDir, $processor, $namer, array $sources, array $destinations)
    {
        return new Profile(
            $scratchDir,
            $this->getProcessor($processor),
            $this->getNamer($namer),
            $this->getSources($sources),
            $this->getDestinations($destinations)
        );
    }

    /**
     * @param string $name
     * @param Source $source
     */
    public function addSource($name, Source $source)
    {
        $this->sources[$name] = $source;
    }

    /**
     * @param string $name
     *
     * @return Source
     */
    public function getSource($name)
    {
        if (isset($this->sources[$name])) {
            throw new \InvalidArgumentException(sprintf('Source "%s" is not registered.', $name));
        }

        return $this->sources[$name];
    }

    /**
     * @param array $names
     *
     * @return Source[]
     */
    public function getSources(array $names)
    {
        $self = $this;

        return array_map(function ($name) use ($self) {
            return $self->getSource($name);
        }, $names);
    }

    /**
     * @param string $name
     * @param Namer  $namer
     */
    public function addNamer($name, Namer $namer)
    {
        $this->namers[$name] = $namer;
    }

    /**
     * @param string $name
     *
     * @return Namer
     */
    public function getNamer($name)
    {
        if (isset($this->namers[$name])) {
            throw new \InvalidArgumentException(sprintf('Namer "%s" is not registered.', $name));
        }

        return $this->namers[$name];
    }

    /**
     * @param string    $name
     * @param Processor $processor
     */
    public function addProcessor($name, Processor $processor)
    {
        $this->processors[$name] = $processor;
    }

    /**
     * @param string $name
     *
     * @return Processor
     */
    public function getProcessor($name)
    {
        if (isset($this->processors[$name])) {
            throw new \InvalidArgumentException(sprintf('Processor "%s" is not registered.', $name));
        }

        return $this->processors[$name];
    }

    /**
     * @param string      $name
     * @param Destination $destination
     */
    public function addDestination($name, Destination $destination)
    {
        $this->destinations[$name] = $destination;
    }

    /**
     * @param string $name
     *
     * @return Destination
     */
    public function getDestination($name)
    {
        if (isset($this->destinations[$name])) {
            throw new \InvalidArgumentException(sprintf('Destination "%s" is not registered.', $name));
        }

        return $this->destinations[$name];
    }

    /**
     * @param array $names
     *
     * @return Destination[]
     */
    public function getDestinations(array $names)
    {
        $self = $this;

        return array_map(function ($name) use ($self) {
            return $self->getDestination($name);
        }, $names);
    }
}
