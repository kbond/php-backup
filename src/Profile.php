<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Profile
{
    private array $sources;
    private array $destinations;

    /**
     * @param Source[]      $sources
     * @param Destination[] $destinations
     */
    public function __construct(
        private string $name,
        private string $scratchDir,
        private Processor $processor,
        private Namer $namer,
        array $sources,
        array $destinations
    ) {
        foreach ($sources as $source) {
            $this->addSource($source);
        }

        foreach ($destinations as $destination) {
            $this->addDestination($destination);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScratchDir(): string
    {
        return $this->scratchDir;
    }

    public function getNamer(): Namer
    {
        return $this->namer;
    }

    public function getProcessor(): Processor
    {
        return $this->processor;
    }

    /**
     * @return Source[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @return Destination[]
     */
    public function getDestinations(): array
    {
        return $this->destinations;
    }

    private function addSource(Source $source): void
    {
        $this->sources[$source->getName()] = $source;
    }

    private function addDestination(Destination $destination): void
    {
        $this->destinations[$destination->getName()] = $destination;
    }
}
