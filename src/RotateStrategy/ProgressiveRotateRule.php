<?php

namespace Zenstruck\Backup\RotateStrategy;

use Zenstruck\Backup\Backup;

/**
 * @author Alexander Kachkaev <alexander@kachkaev.ru>
 */
final class ProgressiveRotateRule
{
    private $frequency;
    private $maxAge;

    /**
     * @param string|null $frequencyAsString
     * @param string|null $maxAgeAsString
     *
     * @return Backup
     */
    public static function createFromStrings($frequencyAsString, $maxAgeAsString)
    {
        $frequency = new ProgressiveRotateFrequency($frequencyAsString);
        $maxAge = new ProgressiveRotateMaxAge($maxAgeAsString);

        return new self($frequency, $maxAge);
    }

    /**
     * @param ProgressiveRotateFrequency $frequency
     * @param ProgressiveRotateMaxAge    $maxAge
     */
    public function __construct(ProgressiveRotateFrequency $frequency, ProgressiveRotateMaxAge $maxAge)
    {
        $this->frequency = $frequency;
        $this->maxAge = $maxAge;
    }

    /**
     * @param Backup $backup
     */
    public function extractBin(Backup $backup)
    {
        return $this->frequency->extractBin($backup);
    }

    /**
     * @param Backup    $backup
     * @param \DateTime $now
     * @param int       $maxAgeTolerance seconds
     */
    public function shouldKeep(Backup $backup, \DateTime $now, $maxAgeTolerance = 0)
    {
        return $this->maxAge->shouldKeep($backup, $now, $maxAgeTolerance);
    }
}
