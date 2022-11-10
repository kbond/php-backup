<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ProfileBuilder
{
    private array $processors;
    private array $namers;
    private array $sources;
    private array $destinations;

    /**
     * @param Processor[]   $processors
     * @param Namer[]       $namers
     * @param Source[]      $sources
     * @param Destination[] $destinations
     */
    public function __construct(
        array $processors = [],
        array $namers = [],
        array $sources = [],
        array $destinations = []
    ) {
        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }

        foreach ($namers as $namer) {
            $this->addNamer($namer);
        }

        foreach ($sources as $source) {
            $this->addSource($source);
        }

        foreach ($destinations as $destination) {
            $this->addDestination($destination);
        }
    }

    /**
     * @param Source[]      $sources
     * @param Destination[] $destinations
     */
    public function create(string $name, string $scratchDir, string $processor, string $namer, array $sources, array $destinations): Profile
    {
        return new Profile(
            $name,
            $scratchDir,
            $this->getProcessor($processor),
            $this->getNamer($namer),
            $this->getSources($sources),
            $this->getDestinations($destinations)
        );
    }

    public function addSource(Source $source): void
    {
        $this->sources[$source->getName()] = $source;
    }

    public function getSource(string $name): Source
    {
        if (!isset($this->sources[$name])) {
            throw new \InvalidArgumentException(\sprintf('Source "%s" is not registered.', $name));
        }

        return $this->sources[$name];
    }

    /**
     * @return Source[]
     */
    public function getSources(array $names): array
    {
        $self = $this;

        return \array_map(fn($name) => $self->getSource($name), $names);
    }

    public function addNamer(Namer $namer): void
    {
        $this->namers[$namer->getName()] = $namer;
    }

    public function getNamer(string $name): Namer
    {
        if (!isset($this->namers[$name])) {
            throw new \InvalidArgumentException(\sprintf('Namer "%s" is not registered.', $name));
        }

        return $this->namers[$name];
    }

    public function addProcessor(Processor $processor): void
    {
        $this->processors[$processor->getName()] = $processor;
    }

    public function getProcessor(string $name): Processor
    {
        if (!isset($this->processors[$name])) {
            throw new \InvalidArgumentException(\sprintf('Processor "%s" is not registered.', $name));
        }

        return $this->processors[$name];
    }

    public function addDestination(Destination $destination): void
    {
        $this->destinations[$destination->getName()] = $destination;
    }

    public function getDestination(string $name): Destination
    {
        if (!isset($this->destinations[$name])) {
            throw new \InvalidArgumentException(\sprintf('Destination "%s" is not registered.', $name));
        }

        return $this->destinations[$name];
    }

    /**
     * @return Destination[]
     */
    public function getDestinations(array $names): array
    {
        $self = $this;

        return \array_map(fn($name) => $self->getDestination($name), $names);
    }
}
