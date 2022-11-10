<?php

namespace Zenstruck\Backup\Namer;

use Zenstruck\Backup\Namer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TimestampNamer implements Namer
{
    public const DEFAULT_FORMAT = 'YmdHis';
    public const DEFAULT_PREFIX = '';

    private ?\DateTimeZone $timezone;

    public function __construct(private string $name, private string $format = self::DEFAULT_FORMAT, private string $prefix = self::DEFAULT_PREFIX, ?string $timezone = null)
    {
        $this->timezone = $timezone ? new \DateTimeZone($timezone) : null;
    }

    /**
     * @throws \Exception
     */
    public function generate(): string
    {
        $dateTime = new \DateTime('now', $this->timezone);

        return $this->prefix.$dateTime->format($this->format);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
