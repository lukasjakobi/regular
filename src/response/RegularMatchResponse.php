<?php

declare(strict_types=1);

namespace LukasJakobi\Regular\Response;

class RegularMatchResponse
{
    protected array $matches;
    protected int|false $response;

    /**
     * RegularMatchResponse constructor.
     *
     * @param array $matches
     * @param false|int $response
     */
    public function __construct(array $matches, bool|int $response)
    {
        $this->matches = $matches;
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function getMatches(): array
    {
        return isset($this->matches[0]) && is_array($this->matches[0])
            ? $this->matches[0]
            : $this->matches;
    }

    /**
     * @return bool
     */
    public function hasMatches(): bool
    {
        return isset($this->matches[0]) && is_array($this->matches[0])
            ? !empty($this->matches[0])
            : !empty($this->matches);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return isset($this->matches[0]) && is_array($this->matches[0])
            ? count($this->matches[0])
            : count($this->matches);
    }

    /**
     * @return false|int
     */
    public function getResponse(): bool|int
    {
        return $this->response;
    }

    /**
     * Whether the match worked
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->response !== false;
    }

    /**
     * Whether matches were found
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->matches[0]) && is_array($this->matches[0])
            ? $this->response !== 0
            : $this->response === 1;
    }
}