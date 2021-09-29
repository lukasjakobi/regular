<?php

declare(strict_types=1);

namespace LukasJakobi\Regular\Response;

class RegularReplaceResponse
{
    protected string $pattern;
    protected string|array $replacement;
    protected string|array $subject;
    protected int $limit;
    protected int $count;
    protected string|array|null $response;

    /**
     * RegularReplaceResponse constructor.
     *
     * @param string $pattern
     * @param array|string $replacement
     * @param array|string $subject
     * @param int $limit
     * @param int $count
     * @param array|string|null $response
     */
    public function __construct(string $pattern, array|string $replacement, array|string $subject, int $limit, int $count, array|string|null $response)
    {
        $this->pattern = $pattern;
        $this->replacement = $replacement;
        $this->subject = $subject;
        $this->limit = $limit;
        $this->count = $count;
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
     * @return array|string
     */
    public function getReplacement(): array|string
    {
        return $this->replacement;
    }

    /**
     * @return array|string
     */
    public function getSubject(): array|string
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
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return array|string|null
     */
    public function getResponse(): array|string|null
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->response !== null;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->response !== $this->subject;
    }
}