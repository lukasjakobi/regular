<?php

declare(strict_types=1);

namespace LukasJakobi\Regular;

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
     * Override the pattern value
     *
     * @param string $pattern
     * @return self
     */
    public function pattern(string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Set the modifier type
     *
     * @param string $modifier
     * @return self
     */
    public function modifier(string $modifier): self
    {
        $this->modifier = $modifier;

        return $this;
    }

    /**
     * Set the delimiter character
     *
     * @param string $delimiter
     * @return self
     */
    public function delimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
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
        $this->pattern .= sprintf('[%s-%s]', $m, $n);

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
     * Returns the complete regular expression including
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
     * @param string $subject
     * @return RegularResult
     */
    public function matches(string $subject): RegularResult
    {
        $matches = [];
        $isMatching = preg_match($this->toExpression(), $subject, $matches) === 1;

        return new RegularResult($matches, $isMatching);
    }

    /**
     * Get the modifier of the regular expression
     *
     * @return string
     */
    public function getModifier(): string
    {
        return $this->modifier;
    }

    /**
     * Get the delimeter of the regular expression
     *
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * Get the pattern of the regular expression
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }
}