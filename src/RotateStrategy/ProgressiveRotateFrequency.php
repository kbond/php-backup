<?php

namespace Zenstruck\Backup\RotateStrategy;

use Zenstruck\Backup\Backup;

/**
 * @author Alexander Kachkaev <alexander@kachkaev.ru>
 */
final class ProgressiveRotateFrequency
{
    /** @var \DateInterval */
    private $valueAsInterval;
    private $valueAsString;

    private static $frequencyFeaturesByType = array(
        'second' => array(
            'propertyValueFactorOf' => 60,
            'baseFormat' => 'Y-m-d H:i:00',
        ),
        'minute' => array(
            'propertyValueFactorOf' => 60,
            'baseFormat' => 'Y-m-d H:00:00',
        ),
        'hour' => array(
            'propertyValueFactorOf' => 24,
            'baseFormat' => 'Y-m-d 00:00:00',
        ),
        'day' => array(
            'propertyValueMax' => 31,
            'baseFormat' => 'Y-01-01 00:00:00',
        ),
        'week' => array(
            'propertyValueMax' => 52,
            'baseFormat' => '\f\i\r\s\t\ \m\o\n\d\a\y\ \o\f \j\a\n Y 00:00:00',
        ),
        'month' => array(
            'propertyValueMax' => 12,
            'baseFormat' => 'Y-01-01 00:00:00',
        ),
        'year' => array(
            'propertyName' => 'y',
            'baseFormat' => '2000-01-01 00:00:00',
        ),
    );
    private $frequencyNumericValue;
    private $frequencyType;
    private $frequencyFeatures;

    /**
     * @param string|null $valueAsString
     */
    public function __construct($valueAsString)
    {
        $this->reset($valueAsString);
    }

    public function reset($valueAsString)
    {
        $this->valueAsString = $valueAsString;

        if ($valueAsString === null || $valueAsString === 'all') {
            $this->valueAsInterval = null;
            $this->frequencyTypeName = null;
            $frequencyTypeFeatures = null;
        } else {
            try {
                $this->valueAsInterval = \DateInterval::createFromDateString($valueAsString);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(sprintf('Value for ProgressiveRotateFrequency could not be parsed as a date interval; %s given', var_export($valueAsString, true)));
            }

            $matchInFrequencyTypesFound = false;
            foreach (self::$frequencyFeaturesByType as $frequencyType => $frequencyFeatures) {
                $regexp = sprintf('/^(\d+) (%s)(s)?$/', $frequencyType);
                if (preg_match($regexp, $this->valueAsString, $matches)) {
                    $this->frequencyNumericValue = $matches[1] * 1;
                    $this->frequencyType = $frequencyType;
                    $this->frequencyFeatures = $frequencyFeatures;
                    $matchInFrequencyTypesFound = true;
                    break;
                }
            }
            if (!$matchInFrequencyTypesFound) {
                throw new \InvalidArgumentException(sprintf('Value for ProgressiveRotateFrequency must have a simple format, e.g. \'10 seconds\'; %s given', var_export($valueAsString, true)));
            }

            if (array_key_exists('propertyValueFactorOf', $this->frequencyFeatures) && $this->frequencyFeatures['propertyValueFactorOf'] % $this->frequencyNumericValue !== 0) {
                throw new \InvalidArgumentException(sprintf('Numer of %ss must be a factor of %d; %d given', $this->frequencyType, $this->frequencyFeatures['propertyValueFactorOf'], $this->frequencyNumericValue));
            }

            if (array_key_exists('propertyValueMax', $this->frequencyFeatures) && $this->frequencyNumericValue > $this->frequencyFeatures['propertyValueMax']) {
                throw new \InvalidArgumentException(sprintf('Numer of %ss must not be greater than %d; %d given', $this->frequencyType, $this->frequencyFeatures['propertyValueMax'], $this->frequencyNumericValue));
            }
        }
    }

    public function extractBin(Backup $backup)
    {
        if (!$this->valueAsInterval) {
            return $backup->getKey();
        } else {
            $createdAt = $backup->getCreatedAt();
            $bin = new \DateTime();
            $bin->setTimestamp(strtotime($createdAt->format($this->frequencyFeatures['baseFormat'])));

            $nextBin = new \DateTime();
            for ($nextBin = new \DateTime(), $nextBin->setTimestamp($bin->getTimestamp()); $nextBin < $createdAt; $nextBin = $nextBin->add($this->valueAsInterval)) {
                $bin->setTimestamp($nextBin->getTimestamp());
            }

            return $bin->format('U');
        }
    }
}
