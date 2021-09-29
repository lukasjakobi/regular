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
     * @param string $pattern
     * @return self
     */
    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getReplacement(): array|string
    {
        return $this->replacement;
    }

    /**
     * @param array|string $replacement
     * @return self
     */
    public function setReplacement(array|string $replacement): self
    {
        $this->replacement = $replacement;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getSubject(): array|string
    {
        return $this->subject;
    }

    /**
     * @param array|string $subject
     * @return self
     */
    public function setSubject(array|string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return self
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return self
     */
    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getResponse(): array|string|null
    {
        return $this->response;
    }

    /**
     * @param array|string|null $response
     * @return self
     */
    public function setResponse(array|string|null $response): self
    {
        $this->response = $response;
        return $this;
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