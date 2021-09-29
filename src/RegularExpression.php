<?php

declare(strict_types=1);

namespace LukasJakobi\Regular;

use LukasJakobi\Regular\Response\RegularGrepResponse;
use LukasJakobi\Regular\Response\RegularMatchResponse;
use LukasJakobi\Regular\Response\RegularReplaceResponse;
use LukasJakobi\Regular\Response\RegularSplitResponse;

class RegularExpression
{
    protected string $pattern, $modifier, $delimiter;

    /**
     * RegularExpression constructor.
     *
     * @param string $pattern the regular expression, leave empty if you want to use the pattern builder
     * @param string $delimiter the regular delimiter
     * @param string $modifier the regular modifier mode
     */
    public function __construct(
        string $pattern = '',
        string $delimiter = RegularDelimiter::SLASH,
        string $modifier = RegularModifier::NONE
    ) {
        $this->pattern = $pattern;
        $this->modifier = $modifier;
        $this->delimiter = $delimiter;
    }

    /**
     * Add a digit between m and n to the pattern
     *
     * @param int $m
     * @param int $n
     * @return self
     */
    public function between(int $m, int $n): self
    {
        $this->pattern .= sprintf('[%s-%s]', min($m, $n), max($m, $n));

        return $this;
    }

    /**
     * Add digit to the pattern
     *
     * @param int|null $number
     * @return self
     */
    public function digit(int $number = null): self
    {
        $this->pattern .= sprintf('[%s]', $number ?? '0-9');

        return $this;
    }

    /**
     * Exclude digit from the pattern
     *
     * @param int|null $number
     * @return self
     */
    public function notDigit(int $number = null): self
    {
        $this->pattern .= sprintf('[^%s]', $number ?? '0-9');

        return $this;
    }

    /**
     * Add char to the pattern
     *
     * @param string $char
     * @return self
     */
    public function char(string $char): self
    {
        if (strlen($char) > 1) {
            $this->pattern .= sprintf('[%s]', $char);
        } else {
            $this->pattern .= $char;
        }

        return $this;
    }

    /**
     * Exclude char from the pattern
     *
     * @param string $char
     * @return self
     */
    public function notChar(string $char): self
    {
        $this->pattern .= sprintf('[^%s]', $char);

        return $this;
    }

    /**
     * Add charset to the pattern
     *
     * @param string $charset
     * @return self
     */
    public function charset(string $charset): self
    {
        if (strlen($charset) === 1) {
            return $this->char($charset);
        }

        $this->pattern .= $charset;

        return $this;
    }

    /**
     * Add a custom regular expression part to the pattern
     *
     * @param string $pattern
     * @return $this
     */
    public function add(string $pattern): self
    {
        $this->pattern .= $pattern;

        return $this;
    }

    /**
     * Add a group expression to the pattern
     *
     * @param RegularGroup $group the group to add to the
     * @return $this
     */
    public function group(RegularGroup $group): self
    {
        $this->pattern .= sprintf('(%s)', $group->getPattern());

        return $this;
    }

    /**
     * Repeats the last instruction of the pattern a minimum of m and a maximum of n times
     *
     * @param int $m minimum amount of repeats
     * @param int|null $n maximum amount of repeats
     * @return self
     */
    public function repeat(int $m, int $n = null): self
    {
        if ($n === null) {
            $this->pattern .= sprintf('{%s}', $m);
        } else {
            $this->pattern .= sprintf('{%s,%s}', $m, $n);
        }

        return $this;
    }

    /**
     * Add whitespace to the pattern
     *
     * @return $this
     */
    public function whitespace(): self
    {
        $this->pattern .= '\s';

        return $this;
    }

    /**
     * Only match subjects that start with this pattern
     *
     * @return $this
     */
    public function startOfString(): self
    {
        $this->pattern .= '^';

        return $this;
    }

    /**
     * Only match subjects that end with this pattern
     *
     * @return $this
     */
    public function endOfString(): self
    {
        $this->pattern .= '$';

        return $this;
    }

    /**
     * Returns the complete regular expression including delimiter
     *
     * @return string
     */
    public function toExpression(): string
    {
        return $this->delimiter . $this->pattern . $this->delimiter . $this->modifier;
    }

    /**
     * Matches subject against pattern
     *
     * @param string $subject the text to search in
     * @param int $flags custom flags
     * @param int $offset custom offset
     * @return RegularMatchResponse
     */
    public function matches(string $subject, int $flags = 0, int $offset = 0): RegularMatchResponse
    {
        $pattern = $this->toExpression();
        $matches = [];
        $response = preg_match($pattern, $subject, $matches, $flags, $offset);

        return new RegularMatchResponse($pattern, $subject, $matches, $flags, $offset, $response);
    }

    /**
     * Matches subject against pattern
     *
     * @param string $subject the text to search in
     * @param int $flags custom flags
     * @param int $offset custom offset
     * @return RegularMatchResponse
     */
    public function matchesAll(string $subject, int $flags = 0, int $offset = 0): RegularMatchResponse
    {
        $pattern = $this->toExpression();
        $matches = [];
        $response = preg_match_all($pattern, $subject, $matches, $flags, $offset);

        return new RegularMatchResponse($pattern, $subject, $matches, $flags, $offset, $response);
    }

    /**
     * Replaces matches of pattern against subject with replacement
     *
     * @param string|array $replacement the string to replace with
     * @param string|array $subject the text to search in
     * @param int $limit limit of results (leave empty to get all results)
     * @return RegularReplaceResponse
     */
    public function replace(string|array $replacement, string|array $subject, int $limit = -1): RegularReplaceResponse
    {
        $pattern = $this->toExpression();
        $count = 0;
        $result = preg_replace($pattern, $replacement, $subject, $limit, $count);

        return new RegularReplaceResponse($pattern, $replacement, $subject, $limit, $count, $result);
    }

    /**
     * Grep matches of pattern in subject
     *
     * @param array $array the array to grep out of
     * @param int $flags custom flags
     * @return RegularGrepResponse
     */
    public function grep(array $array, int $flags = 0): RegularGrepResponse
    {
        $pattern = $this->toExpression();
        $result = preg_grep($pattern, $array, $flags);

        return new RegularGrepResponse($pattern, $array, $flags, $result);
    }

    /**
     * Split a charset using pattern
     *
     * @param string $subject
     * @param int $limit
     * @param int $flags custom flags
     * @return RegularSplitResponse
     */
    public function split(string $subject, int $limit = -1, int $flags = 0): RegularSplitResponse
    {
        $pattern = $this->toExpression();
        $response = preg_split($pattern, $subject, $limit, $flags);

        return new RegularSplitResponse($pattern, $subject, $limit, $flags, $response);
    }

    /**
     * Quote a string to escape regular expression syntax
     *
     * @param string $string
     * @param string|null $delimiter
     * @return string
     */
    public function quote(string $string, ?string $delimiter = null): string
    {
        return preg_quote($string, $delimiter);
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
     * @return string
     */
    public function getModifier(): string
    {
        return $this->modifier;
    }

    /**
     * @param string $modifier
     * @return self
     */
    public function setModifier(string $modifier): self
    {
        $this->modifier = $modifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return self
     */
    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;
        return $this;
    }
}