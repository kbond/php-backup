<?php

namespace Zenstruck\Backup;

use Traversable;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ProfileRegistry implements \Countable, \IteratorAggregate
{
    /** @var Profile[] */
    private array $profiles = [];

    /**
     * @param Profile[] $profiles
     */
    public function __construct(array $profiles = [])
    {
        foreach ($profiles as $profile) {
            $this->add($profile);
        }
    }

    public function add(Profile $profile)
    {
        $this->profiles[$profile->getName()] = $profile;
    }

    public function get(string $name): Profile
    {
        if (!isset($this->profiles[$name])) {
            throw new \InvalidArgumentException(sprintf('Profile "%s" is not registered.', $name));
        }

        return $this->profiles[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->profiles);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->profiles);
    }
}
