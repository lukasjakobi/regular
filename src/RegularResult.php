<?php

declare(strict_types=1);

namespace LukasJakobi\Regular;

class RegularResult
{
    protected array|string $matches;
    protected bool $valid;

    /**
     * RegularResult constructor.
     *
     * @param array|string $result
     * @param bool $valid
     */
    public function __construct(array|string $result, bool $valid)
    {
        $this->matches = $result;
        $this->valid = $valid;
    }

    /**
     * The matches of your search
     *
     * @return array|string
     */
    public function matches(): array|string
    {
        return $this->matches;
    }

    /**
     * The amount of times, a pattern did match
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->matches);
    }

    /**
     * Has the match been successful
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->valid;
    }
}
