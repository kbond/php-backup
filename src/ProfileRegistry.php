<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ProfileRegistry implements \Countable, \IteratorAggregate
{
    /** @var Profile[] */
    private $profiles = array();

    /**
     * @param Profile[] $profiles
     */
    public function __construct(array $profiles = array())
    {
        foreach ($profiles as $profile) {
            $this->add($profile);
        }
    }

    /**
     * @param Profile $profile
     */
    public function add(Profile $profile)
    {
        $this->profiles[$profile->getName()] = $profile;
    }

    /**
     * @param string $name
     *
     * @return Profile
     */
    public function get($name)
    {
        if (!isset($this->profiles[$name])) {
            throw new \InvalidArgumentException(sprintf('Profile "%s" is not registered.', $name));
        }

        return $this->profiles[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->profiles);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->profiles);
    }
}
