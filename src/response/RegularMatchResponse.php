<?php

declare(strict_types=1);

namespace LukasJakobi\Regular\Response;

class RegularMatchResponse
{
    protected string $pattern;
    protected string $subject;
    protected array $matches;
    protected int $flags;
    protected int $offset;
    protected int|false $response;

    /**
     * RegularMatchResponse constructor.
     *
     * @param string $pattern
     * @param string $subject
     * @param array $matches
     * @param int $flags
     * @param int $offset
     * @param int|false $response
     */
    public function __construct(string $pattern, string $subject, array $matches, int $flags, int $offset, int|false $response)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->matches = $matches;
        $this->flags = $flags;
        $this->offset = $offset;
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
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
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
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