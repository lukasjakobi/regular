<?php

declare(strict_types=1);

namespace LukasJakobi\Regular\Response;

class RegularSplitResponse
{
    protected string $pattern;
    protected string $subject;
    protected int $limit;
    protected int $flags;
    protected array|false $response;

    /**
     * RegularSplitResponse constructor.
     * @param string $pattern
     * @param string $subject
     * @param int $limit
     * @param int $flags
     * @param array|false $response
     */
    public function __construct(string $pattern, string $subject, int $limit, int $flags, bool|array $response)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->limit = $limit;
        $this->flags = $flags;
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
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @return array|bool
     */
    public function getResponse(): array|bool
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->response);
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
        return $this->response !== false && count($this->response) > 0;
    }
}