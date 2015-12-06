<?php

namespace Zenstruck\Backup\RotateStrategy;

/**
 * @author Alexander Kachkaev <alexander@kachkaev.ru>
 */
final class ProgressiveRotateRuleCollection implements \IteratorAggregate, \Countable
{
    /** @var ProgressiveRotateRule[] */
    private $rules;


    public static function createFromArray(array $rules)
    {
        $parsedRules = array();

        foreach ($rules as &$rule) {
            array_push($parsedRules, ProgressiveRotateRule::createFromStrings(@$rule['frequency'], @$rule['max_age']));
        }

        return new self($parsedRules);
    }

    /**
     * @param ProgressiveRotateRule[] $rules
     */
    public function __construct(array $rules)
    {
        $count = count($rules);
        if (array_keys($rules) !== range(0, $count - 1)) {
            throw new \InvalidArgumentException(sprintf('ProgressiveRotateRuleCollection::__construct only accepts sequential arrays'));
        }
        for ($i = 0; $i < $count; ++$i) {
            if (!$rules[$i] instanceof ProgressiveRotateRule) {
                throw new \InvalidArgumentException(sprintf('$rules[%d] in ProgressiveRotateRuleCollection is not an instance of ProgressiveRotateRule', $i));
            }
        }

        $this->rules = $rules;
    }

    /**
     * @param int $key
     *
     * @return ProgressiveRotateRule
     */
    public function get($key)
    {
        return $this->rules[$key];
    }

    /**
     * @return ProgressiveRotateRule[]
     */
    public function all()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->rules);
    }
}
