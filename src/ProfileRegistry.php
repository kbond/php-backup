<?php

namespace Zenstruck\Backup;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ProfileRegistry
{
    /** @var Profile[] */
    private $profiles;

    /**
     * @param Profile[] $profiles
     */
    public function __construct(array $profiles = array())
    {
        $this->profiles = $profiles;
    }

    /**
     * @param string  $name
     * @param Profile $profile
     */
    public function add($name, Profile $profile)
    {
        $this->profiles[$name] = $profile;
    }

    /**
     * @param string $name
     *
     * @return Profile
     */
    public function get($name)
    {
        if (isset($this->profiles[$name])) {
            throw new \InvalidArgumentException(sprintf('Profile "%s" is not registered.', $name));
        }

        return $this->profiles[$name];
    }

    /**
     * @return Profile[]
     */
    public function all()
    {
        return $this->profiles;
    }
}
