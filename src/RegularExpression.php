<?php

declare(strict_types=1);

namespace LukasJakobi\Regular;

use LukasJakobi\Regular\Response\RegularMatchResponse;

class RegularExpression
{
    protected string $pattern, $delimiter;
    protected string|array $modifiers;

    /**
     * RegularExpression constructor.
     *
     * @param string $pattern the regular expression, leave empty if you want to use the pattern builder
     * @param string $delimiter the regular delimiter
     * @param string|array $modifiers the regular modifier mode(s)
     */
    public function __construct(
        string $pattern = '',
        string $delimiter = RegularDelimiter::SLASH,
        string|array $modifiers = RegularModifier::NONE
    ) {
        $this->pattern = $pattern;
        $this->modifiers = $modifiers;
        $this->delimiter = $delimiter;
    }

    /**
     * Add a digit between m and n to the pattern
     *
     * @param int $m
     * @param int $n
     * @return self
     */
    public function addDigitBetween(int $m, int $n): self
    {
        $this->pattern .= sprintf('[%s-%s]', min($m, $n), max($m, $n));

        return $this;
    }

    /**
     * Add a digit to the pattern
     *
     * @param int $number
     * @return self
     * @throws RegularError
     */
    public function addDigit(int $number): self
    {
        if ($number > 9 || $number < 0) {
            throw new RegularError(RegularError::DIGIT_RANGE_ERROR);
        }

        $this->pattern .= sprintf('[%s]', $number);

        return $this;
    }

    /**
     * Add digit to the pattern
     *
     * @return self
     */
    public function addAnyDigit(): self
    {
        $this->pattern .= '\d';

        return $this;
    }

    /**
     * Exclude digit from the pattern
     *
     * @return self
     */
    public function addNoDigit(): self
    {
        $this->pattern .= '\D';

        return $this;
    }

    /**
     * Add character range to pattern
     *
     * @param string $charFrom
     * @param string $charTo
     * @return self
     * @throws RegularError
     */
    public function addCharRange(string $charFrom, string $charTo): self
    {
        if (strlen($charFrom) > 1 || strlen($charTo) > 1) {
            throw new RegularError(RegularError::CHARACTER_LENGTH_ERROR);
        }

        $this->pattern .= sprintf('[%s-%s]', $charFrom, $charTo);

        return $this;
    }

    /**
     * Add char to the pattern
     *
     * @param string $chars
     * @return self
     */
    public function addChars(string $chars): self
    {
        if (strlen($chars) > 1) {
            $this->pattern .= sprintf('[%s]', $chars);
        } else {
            $this->pattern .= $chars;
        }

        return $this;
    }

    /**
     * Exclude char from the pattern
     *
     * @param string $chars
     * @return self
     */
    public function addCharsExcept(string $chars): self
    {
        $this->pattern .= sprintf('[^%s]', $chars);

        return $this;
    }

    /**
     * Add any character to the pattern
     *
     * @return self
     */
    public function addAnyCharacter(): self
    {
        $this->pattern .= '.';

        return $this;
    }

    /**
     * Add previous expressions zero or more times to the pattern
     *
     * @return self
     */
    public function addZeroOrMoreTimes(): self
    {
        $this->pattern .= '*';

        return $this;
    }

    /**
     * Add previous expressions one or more times to the pattern
     *
     * @return self
     */
    public function addOneOrMoreTimes(): self
    {
        $this->pattern .= '+';

        return $this;
    }

    /**
     * Add previous expressions one or more times to the pattern
     *
     * @return self
     */
    public function addZeroOrOneTimes(): self
    {
        $this->pattern .= '?';

        return $this;
    }

    /**
     * Makes the previous expression optional
     *
     * @return self
     */
    public function addOptionality(): self
    {
        $this->pattern .= '?';

        return $this;
    }

    /**
     * Add an alternate operator
     *
     * @param array $words
     * @return self
     */
    public function addAlternate(array $words): self
    {
        $this->pattern .= sprintf('(%s)', implode('|', $words));

        return $this;
    }

    /**
     * Add a custom regular expression part to the pattern
     *
     * @param string $pattern
     * @return $this
     */
    public function addCustom(string $pattern): self
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
    public function addCapturingGroup(RegularGroup $group): self
    {
        $this->pattern .= sprintf('(%s)', $group->getPattern());

        return $this;
    }

    /**
     * Add a named group expression to the pattern
     *
     * @param string $name the name of the group
     * @param RegularGroup $group the group to add to the
     * @return $this
     */
    public function addNamedCapturingGroup(string $name, RegularGroup $group): self
    {
        $this->pattern .= sprintf('(<%s>%s)', $name, $group->getPattern());

        return $this;
    }

    /**
     * Add a group expression to the pattern
     *
     * @param RegularGroup $group the group to add to the
     * @return $this
     */
    public function addNonCapturingGroup(RegularGroup $group): self
    {
        $this->pattern .= sprintf('(?:%s)', $group->getPattern());

        return $this;
    }

    /**
     * Repeats the last instruction of the pattern a minimum of m and a maximum of n times
     *
     * @param int $from minimum amount of repeats
     * @param int $to maximum amount of repeats
     * @return self
     */
    public function repeatBetween(int $from, int $to): self
    {
        $this->pattern .= sprintf('{%s,%s}', $from, $to);

        return $this;
    }

    /**
     * Repeats a pattern at least $times times
     *
     * @param int $times
     * @return $this
     */
    public function repeatAtLeast(int $times): self
    {
        $this->pattern .= sprintf('{%s,}', $times);

        return $this;
    }

    /**
     * Repeats a pattern at most $times times
     *
     * @param int $times
     * @return $this
     */
    public function repeatAtMost(int $times): self
    {
        $this->pattern .= sprintf('{0,%s}', $times);

        return $this;
    }

    /**
     * Repeats a pattern exactly $times times
     *
     * @param int $times
     * @return $this
     */
    public function repeatExactly(int $times): self
    {
        $this->pattern .= sprintf('{%s}', $times);

        return $this;
    }

    /**
     * Add whitespace to the pattern
     *
     * @return $this
     */
    public function addWhitespace(): self
    {
        $this->pattern .= '\s';

        return $this;
    }

    /**
     * Add a linebreak operator
     *
     * @return self
     */
    public function addLinebreak(): self
    {
        $this->pattern .= '\n';

        return $this;
    }

    /**
     * Add a tab operator
     *
     * @return self
     */
    public function addTab(): self
    {
        $this->pattern .= '\t';

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
        return $this->delimiter . $this->pattern . $this->delimiter . $this->toModifier($this->modifiers);
    }

    /**
     * Returns the modifier array as a string if necessary
     *
     * @param string|array $modifiers
     * @return string
     */
    private function toModifier(string|array $modifiers): string
    {
        if (is_array($modifiers)) {
            return implode($modifiers);
        }

        return $modifiers;
    }

    /**
     * Matches subject against pattern
     *
     * @param string $subject the text to search in, will be quoted
     * @param int $flags custom flags
     * @param int $offset custom offset
     * @return RegularMatchResponse
     */
    public function matches(string $subject, int $flags = 0, int $offset = 0): RegularMatchResponse
    {
        $subject = $this->quote($subject);
        $pattern = $this->toExpression();
        $matches = [];
        $response = preg_match($pattern, $subject, $matches, $flags, $offset);

        return new RegularMatchResponse($matches, $response);
    }

    /**
     * Matches subject against pattern
     *
     * @param string $subject the text to search in, will be quoted
     * @param int $flags custom flags
     * @param int $offset custom offset
     * @return RegularMatchResponse
     */
    public function matchesAll(string $subject, int $flags = 0, int $offset = 0): RegularMatchResponse
    {
        $subject = $this->quote($subject);
        $pattern = $this->toExpression();
        $matches = [];
        $response = preg_match_all($pattern, $subject, $matches, $flags, $offset);

        return new RegularMatchResponse($matches, $response);
    }

    /**
     * Replaces matches of pattern against subject with replacement
     *
     * @param string|array $replacement the string to replace with
     * @param string|array $subject the text to search in
     * @param int $limit limit of results (leave empty to get all results)
     * @return array|string
     */
    public function replace(string|array $replacement, string|array $subject, int $limit = -1): array|string
    {
        $count = 0;

        return preg_replace($this->toExpression(), $replacement, $subject, $limit, $count);
    }

    /**
     * Grep matches of pattern in subject
     *
     * @param array $array the array to grep out of
     * @param int $flags custom flags
     * @return array|false
     */
    public function grep(array $array, int $flags = 0):  array|false
    {
        return preg_grep($this->toExpression(), $array, $flags);
    }

    /**
     * Split a charset using pattern
     *
     * @param string $subject
     * @param int $limit
     * @param int $flags custom flags
     * @return array|false
     */
    public function split(string $subject, int $limit = -1, int $flags = 0): array|false
    {
        return preg_split($this->toExpression(), $subject, $limit, $flags);
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
     * @return string|array
     */
    public function getModifiers(): string|array
    {
        return $this->modifiers;
    }

    /**
     * @param string|array $modifiers
     * @return self
     */
    public function setModifiers(string|array $modifiers): self
    {
        $this->modifiers = $modifiers;
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