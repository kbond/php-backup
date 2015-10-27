<?php

namespace Zenstruck\Backup\Namer;

use Zenstruck\Backup\Namer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TimestampNamer implements Namer
{
    const DEFAULT_FORMAT = 'YmdHis';
    const DEFAULT_PREFIX = '';

    private $name;
    private $format;
    private $prefix;
    private $timezone;

    /**
     * @param string      $name
     * @param string      $format
     * @param string      $prefix
     * @param string|null $timezone
     */
    public function __construct($name, $format = self::DEFAULT_FORMAT, $prefix = self::DEFAULT_PREFIX, $timezone = null)
    {
        $this->name = $name;
        $this->format = $format;
        $this->prefix = $prefix;
        $this->timezone = $timezone ? new \DateTimeZone($timezone) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $dateTime = new \DateTime('now', $this->timezone);

        return $this->prefix.$dateTime->format($this->format);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
