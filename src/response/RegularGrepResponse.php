<?php

declare(strict_types=1);

namespace LukasJakobi\Regular\Response;

class RegularGrepResponse
{
    protected string $pattern;
    protected array $array;
    protected int $flags;
    protected array|false $response;

    /**
     * RegularGrepResponse constructor.
     *
     * @param string $pattern
     * @param array $array
     * @param int $flags
     * @param array|false $response
     */
    public function __construct(string $pattern, array $array, int $flags, bool|array $response)
    {
        $this->pattern = $pattern;
        $this->array = $array;
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
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
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
        return array_values($this->response);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->response);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->response !== false;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return !($this->response === false) && count($this->response);
    }
}