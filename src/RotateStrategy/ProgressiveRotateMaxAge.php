<?php

namespace Zenstruck\Backup\RotateStrategy;

use Zenstruck\Backup\Backup;

/**
 * @author Alexander Kachkaev <alexander@kachkaev.ru>
 */
final class ProgressiveRotateMaxAge
{
    /** @var \DateInterval */
    private $valueAsInterval;
    private $valueAsString;

    /**
     * @param string $valueAsString
     */
    public function __construct($valueAsString)
    {
        $this->reset($valueAsString);
    }

    public function reset($valueAsString)
    {
        $this->valueAsString = $valueAsString;

        if ($valueAsString === null || $valueAsString === 'forever') {
            $this->valueAsInterval = null;
        } else {
            try {
                $this->valueAsInterval = \DateInterval::createFromDateString($valueAsString);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(sprintf('Value for ProgressiveRotateMaxAge cannot be parsed as a date interval; %s given', var_export($valueAsString, true)));
            }

            if ($this->valueAsInterval->invert) {
                throw new \InvalidArgumentException(sprintf('Value for ProgressiveRotateMaxAge cannot be a negative date interval; %s given', var_export($valueAsString, true)));
            }
        }
    }

    /**
     * @param Backup    $backup
     * @param \DateTime $now
     * @param int       $maxAgeTolerance seconds
     */
    public function shouldKeep(Backup $backup, \DateTime $now, $maxAgeTolerance = 0)
    {
        if ($this->valueAsInterval === null) {
            return true;
        } else {
            $tmp = new \DateTime();
            $tmp->setTimestamp($now->getTimestamp());
            $tmp->sub($this->valueAsInterval);

            return $tmp->getTimestamp() - $maxAgeTolerance <= $backup->getCreatedAt()->getTimestamp();
        }
    }
}
