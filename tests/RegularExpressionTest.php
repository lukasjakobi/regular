<?php

declare(strict_types=1);

namespace LukasJakobi\Regular\Test;

use LukasJakobi\Regular\RegularDelimiter;
use LukasJakobi\Regular\RegularExpression;
use LukasJakobi\Regular\RegularModifier;
use PHPUnit\Framework\TestCase;

class RegularExpressionTest extends TestCase
{
    /**
     * @param RegularExpression $regularExpression
     * @param string $expected
     *
     * @dataProvider dataRegexBuilder
     */
    public function testRegexBuilder(RegularExpression $regularExpression, string $expected): void
    {
        $this->assertEquals($expected, $regularExpression->toExpression());
    }

    /**
     * @return array
     */
    public function dataRegexBuilder(): array
    {
        return [
            '#1' => [
                (new RegularExpression())
                    ->digit()
                    ->repeat(5)
                    ->whitespace(),
                '/[0-9]{5}\s/'
            ],
            '#2' => [
                (new RegularExpression())
                    ->char('abc')
                    ->notChar('d')
                    ->repeat(2)
                    ->endOfString(),
                '/[abc][^d]{2}$/'
            ],
            '#3' => [
                (new RegularExpression('^[^1-8]'))
                    ->char('abc')
                    ->notChar('b')
                    ->add('.*')
                    ->setModifier(RegularModifier::CASE_INSENSITIVE)
                    ->setDelimiter(RegularDelimiter::HASH),
                '#^[^1-8][abc][^b].*#i'
            ],
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param int $count
     * @param array $matches
     * @param int $flags
     * @param int $offset
     * @param bool $successful
     *
     * @dataProvider dataMatches
     */
    public function testMatches(
        RegularExpression $regularExpression,
        string $subject,
        int $count,
        array $matches,
        int $flags,
        int $offset,
        bool $successful
    ): void {
        $result = $regularExpression->matches($subject, $flags, $offset);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($regularExpression->toExpression(), $result->getPattern());
        $this->assertEquals($flags, $result->getFlags());
        $this->assertEquals($offset, $result->getOffset());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals(($count > 0), $result->hasMatches());
        $this->assertEquals($subject, $result->getSubject());
        $this->assertEquals($matches, $result->getMatches());
        $this->assertIsInt($result->getResponse());
    }

    /**
     * @return array
     */
    public function dataMatches(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->between(6, 9),
                'my favourite number is 7',
                1,
                ['7'],
                0,
                0,
                true
            ],
            '#2' => [
                (new RegularExpression())->between(6, 9),
                'my favourite numbers are 7 and 9',
                1,
                ['7'],
                0,
                0,
                true
            ],
            '#3' => [
                (new RegularExpression())->between(6, 9),
                'no matches',
                0,
                [],
                0,
                0,
                false
            ]
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param int $count
     * @param array $matches
     * @param int $flags
     * @param int $offset
     * @param bool $successful
     *
     * @dataProvider dataMatchesAll
     */
    public function testMatchesAll(
        RegularExpression $regularExpression,
        string $subject,
        int $count,
        array $matches,
        int $flags,
        int $offset,
        bool $successful
    ): void {
        $result = $regularExpression->matchesAll($subject);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($regularExpression->toExpression(), $result->getPattern());
        $this->assertEquals($flags, $result->getFlags());
        $this->assertEquals($offset, $result->getOffset());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals(($count > 0), $result->hasMatches());
        $this->assertEquals($subject, $result->getSubject());
        $this->assertEquals($matches, $result->getMatches());
        $this->assertIsInt($result->getResponse());
    }

    /**
     * @return array
     */
    public function dataMatchesAll(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->between(6, 9)->setDelimiter(RegularDelimiter::HASH),
                'my favourite number is 7',
                1,
                ['7'],
                0,
                0,
                true
            ],
            '#2' => [
                (new RegularExpression())->between(6, 9),
                'my favourite numbers are 7 and 9',
                2,
                ['7', '9'],
                0,
                0,
                true
            ],
            '#3' => [
                (new RegularExpression())->between(6, 9),
                'no matches',
                0,
                [],
                0,
                0,
                false
            ]
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string|array $replacement
     * @param string|array $subject
     * @param string|array $results
     * @param int $limit
     * @param int $count
     * @param bool $successful
     *
     * @dataProvider dataReplace
     */
    public function testReplace(
        RegularExpression $regularExpression,
        string|array $replacement,
        string|array $subject,
        string|array $results,
        int $limit,
        int $count,
        bool $successful
    ): void {
        $result = $regularExpression->replace($replacement, $subject, $limit);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals($results, $result->getResponse());
        $this->assertEquals($subject, $result->getSubject());
        $this->assertEquals($regularExpression->toExpression(), $result->getPattern());
        $this->assertEquals($limit, $result->getLimit());
        $this->assertEquals($replacement, $result->getReplacement());
    }

    /**
     * @return array
     */
    public function dataReplace(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->char('x'),
                '7',
                'my favourite number is x',
                'my favourite number is 7',
                -1,
                1,
                true
            ],
            '#2' => [
                (new RegularExpression())->char('\.'),
                ' ',
                'this.text.is.dotted',
                'this text is.dotted',
                2,
                2,
                true
            ],
            '#3' => [
                (new RegularExpression())->char('x'),
                'replacement',
                'no matches',
                'no matches',
                -1,
                0,
                false
            ],
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param array $array
     * @param array $response
     * @param int $count
     * @param int $flags
     * @param bool $successful
     *
     * @dataProvider dataGrep
     */
    public function testGrep(
        RegularExpression $regularExpression,
        array $array,
        array $response,
        int $count,
        int $flags,
        bool $successful
    ): void {
        $result = $regularExpression->grep($array, $flags);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($regularExpression->toExpression(), $result->getPattern());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals($array, $result->getArray());
        $this->assertEquals($flags, $result->getFlags());
        $this->assertEquals($response, $result->getResponse());
    }

    /**
     * @return array
     */
    public function dataGrep(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->char('a-z'),
                ['a', 'b', 'c', '1'],
                ['a', 'b', 'c'],
                3,
                0,
                true
            ],
            '#2' => [
                (new RegularExpression())->char('x'),
                ['x', 'xxx', 'no'],
                ['no'],
                1,
                1,
                true
            ],
            '#3' => [
                (new RegularExpression())->char('x'),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                3,
                1,
                true
            ],
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param array $results
     * @param int $limit
     * @param int $flags
     * @param int $count
     * @param bool $successful
     *
     * @dataProvider dataSplit
     */
    public function testSplit(
        RegularExpression $regularExpression,
        string $subject,
        array $results,
        int $limit,
        int $flags,
        int $count,
        bool $successful
    ): void {
        $result = $regularExpression->split($subject, $limit, $flags);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals($results, $result->getResponse());
        $this->assertEquals($limit, $result->getLimit());
        $this->assertEquals($flags, $result->getFlags());
        $this->assertEquals($regularExpression->toExpression(), $result->getPattern());
        $this->assertEquals($subject, $result->getSubject());
    }

    /**
     * @return array
     */
    public function dataSplit(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->char('x'),
                'axbxc',
                ['a', 'b', 'c'],
                -1,
                0,
                3,
                true
            ],
            '#2' => [
                (new RegularExpression())->char('d'),
                'derdiedas',
                ['', 'er', 'iedas'],
                3,
                0,
                3,
                true
            ],
            '#3' => [
                (new RegularExpression())->char('x'),
                'no match',
                ['no match'],
                2,
                0,
                1,
                true
            ],
        ];
    }

    /**
     * @param string $unquoted
     * @param string $quoted
     *
     * @dataProvider dataQuote
     */
    public function testQuote(string $unquoted, string $quoted): void
    {
        $result = (new RegularExpression())->quote($unquoted);

        $this->assertEquals($quoted, $result);
    }

    /**
     * @return array
     */
    public function dataQuote(): array
    {
        return [
            '#1' => [
                '/\s[1-8]{9}/i',
                '/\\\s\[1\-8\]\{9\}/i',
            ],
            '#2' => [
                'back\slash',
                'back\\\slash',
            ],
            '#3' => [
                '$40 für einen G3/400',
                '\$40 für einen G3/400',
            ],
            '#4' => [
                '.\+*?[^]$(){}=!<>|:-',
                '\.\\\\\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:\-'
            ],
        ];
    }

    /**
     * @param int $m
     * @param int $n
     * @param string $delimiter
     * @param string $modifier
     *
     * @dataProvider dataBetween
     */
    public function testBetween(int $m, int $n, string $delimiter, string $modifier): void
    {
        $regularExpression = (new RegularExpression())
            ->setDelimiter($delimiter)
            ->setModifier($modifier)
            ->between($m, $n);

        $pattern = sprintf(
            '[%s-%s]',
            min($m, $n),
            max($m, $n)
        );

        $expression = sprintf(
            '%s[%s-%s]%s%s',
            $delimiter,
            min($m, $n),
            max($m, $n),
            $delimiter,
            $modifier
        );

        $this->assertEquals($pattern, $regularExpression->getPattern());
        $this->assertEquals($expression, $regularExpression->toExpression());
        $this->assertEquals($delimiter, $regularExpression->getDelimiter());
        $this->assertEquals($modifier, $regularExpression->getModifier());
    }

    public function dataBetween(): array
    {
        return [
            [1, 2, RegularDelimiter::SLASH, RegularModifier::CASE_INSENSITIVE],
            [5, 3, RegularDelimiter::HASH, RegularModifier::UTF_8_ENCODED],
            [7, 0, RegularDelimiter::PERCENTAGE, RegularModifier::MULTI_LINE],
            [7, 7, RegularDelimiter::PLUS, RegularModifier::NONE],
        ];
    }

    /**
     * @param int|null $n
     * @param string $delimiter
     * @param string $modifier
     *
     * @dataProvider dataDigit
     */
    public function testDigit(?int $n, string $delimiter, string $modifier): void
    {
        $regularExpression = (new RegularExpression())
            ->setDelimiter($delimiter)
            ->setModifier($modifier)
            ->digit($n);

        $pattern = sprintf(
            '[%s]',
            $n ?? '0-9'
        );

        $expression = sprintf(
            '%s[%s]%s%s',
            $delimiter,
            $n ?? '0-9',
            $delimiter,
            $modifier
        );

        $this->assertEquals($pattern, $regularExpression->getPattern());
        $this->assertEquals($expression, $regularExpression->toExpression());
        $this->assertEquals($delimiter, $regularExpression->getDelimiter());
        $this->assertEquals($modifier, $regularExpression->getModifier());
    }

    /**
     * @param int|null $n
     * @param string $delimiter
     * @param string $modifier
     *
     * @dataProvider dataDigit
     */
    public function testNotDigit(?int $n, string $delimiter, string $modifier): void
    {
        $regularExpression = (new RegularExpression())
            ->setDelimiter($delimiter)
            ->setModifier($modifier)
            ->notDigit($n);

        $pattern = sprintf(
            '[^%s]',
            $n ?? '0-9'
        );

        $expression = sprintf(
            '%s[^%s]%s%s',
            $delimiter,
            $n ?? '0-9',
            $delimiter,
            $modifier
        );

        $this->assertEquals($pattern, $regularExpression->getPattern());
        $this->assertEquals($expression, $regularExpression->toExpression());
        $this->assertEquals($delimiter, $regularExpression->getDelimiter());
        $this->assertEquals($modifier, $regularExpression->getModifier());
    }

    public function dataDigit(): array
    {
        return [
            [1, RegularDelimiter::AT, RegularModifier::CASE_INSENSITIVE],
            [5, RegularDelimiter::PLUS, RegularModifier::UTF_8_ENCODED],
            [7, RegularDelimiter::PERCENTAGE, RegularModifier::MULTI_LINE],
            [null, RegularDelimiter::PLUS, RegularModifier::NONE],
        ];
    }

    /**
     * @param string $char
     * @param string $delimiter
     * @param string $modifier
     *
     * @dataProvider dataChar
     */
    public function testChar(string $char, string $delimiter, string $modifier): void
    {
        $regularExpression = (new RegularExpression())
            ->setDelimiter($delimiter)
            ->setModifier($modifier)
            ->char($char);

        $pattern = sprintf(
            strlen($char) > 1 ? '[%s]' : '%s',
            $char
        );

        $expression = sprintf(
            strlen($char) > 1 ? '%s[%s]%s%s' : '%s%s%s%s',
            $delimiter,
            $char,
            $delimiter,
            $modifier
        );

        $this->assertEquals($pattern, $regularExpression->getPattern());
        $this->assertEquals($expression, $regularExpression->toExpression());
        $this->assertEquals($delimiter, $regularExpression->getDelimiter());
        $this->assertEquals($modifier, $regularExpression->getModifier());
    }

    /**
     * @param string $char
     * @param string $delimiter
     * @param string $modifier
     *
     * @dataProvider dataChar
     */
    public function testNotChar(string $char, string $delimiter, string $modifier): void
    {
        $regularExpression = (new RegularExpression())
            ->setDelimiter($delimiter)
            ->setModifier($modifier)
            ->notChar($char);

        $pattern = sprintf(
            '[^%s]',
            $char
        );

        $expression = sprintf(
            '%s[^%s]%s%s',
            $delimiter,
            $char,
            $delimiter,
            $modifier
        );

        $this->assertEquals($pattern, $regularExpression->getPattern());
        $this->assertEquals($expression, $regularExpression->toExpression());
        $this->assertEquals($delimiter, $regularExpression->getDelimiter());
        $this->assertEquals($modifier, $regularExpression->getModifier());
    }

    /**
     * @param string $charset
     * @param string $delimiter
     * @param string $modifier
     *
     * @dataProvider dataChar
     */
    public function testCharset(string $charset, string $delimiter, string $modifier): void
    {
        $regularExpression = (new RegularExpression())
            ->setDelimiter($delimiter)
            ->setModifier($modifier)
            ->charset($charset);

        $pattern = sprintf(
            '%s',
            $charset
        );

        $expression = sprintf(
            '%s%s%s%s',
            $delimiter,
            $charset,
            $delimiter,
            $modifier
        );

        $this->assertEquals($pattern, $regularExpression->getPattern());
        $this->assertEquals($expression, $regularExpression->toExpression());
        $this->assertEquals($delimiter, $regularExpression->getDelimiter());
        $this->assertEquals($modifier, $regularExpression->getModifier());
    }

    public function dataChar(): array
    {
        return [
            ['a', RegularDelimiter::AT, RegularModifier::CASE_INSENSITIVE],
            ['a-z', RegularDelimiter::PLUS, RegularModifier::UTF_8_ENCODED],
            ['v', RegularDelimiter::PERCENTAGE, RegularModifier::MULTI_LINE],
            ['t', RegularDelimiter::PLUS, RegularModifier::NONE],
        ];
    }

    /**
     * @param string $add
     * @param string $delimiter
     * @param string $modifier
     *
     * @dataProvider dataAdd
     */
    public function testAdd(string $add, string $delimiter, string $modifier): void
    {
        $regularExpression = (new RegularExpression())
            ->setDelimiter($delimiter)
            ->setModifier($modifier)
            ->add($add);

        $pattern = sprintf(
            '%s',
            $add
        );

        $expression = sprintf(
            '%s%s%s%s',
            $delimiter,
            $add,
            $delimiter,
            $modifier
        );

        $this->assertEquals($pattern, $regularExpression->getPattern());
        $this->assertEquals($expression, $regularExpression->toExpression());
        $this->assertEquals($delimiter, $regularExpression->getDelimiter());
        $this->assertEquals($modifier, $regularExpression->getModifier());
    }

    public function dataAdd(): array
    {
        return [
            ['a', RegularDelimiter::AT, RegularModifier::CASE_INSENSITIVE],
            ['a-z', RegularDelimiter::PLUS, RegularModifier::UTF_8_ENCODED],
            ['v', RegularDelimiter::PERCENTAGE, RegularModifier::MULTI_LINE],
            ['t', RegularDelimiter::PLUS, RegularModifier::NONE],
        ];
    }

    public function testPattern(): void
    {
        $pattern = '[1-9]';
        $regularExpression = (new RegularExpression())
            ->setPattern('[1-9]');

        $this->assertEquals($pattern, $regularExpression->getPattern());
    }

    public function testStartOfString(): void
    {
        $subject = '187';
        $regularExpression = (new RegularExpression())
            ->startOfString()
            ->setPattern('[1-9]');

        $result = $regularExpression->matches($subject);

        $this->assertTrue($result->isValid());
        $this->assertTrue($result->isSuccessful());
    }

    public function testEndOfString(): void
    {
        $subject = '187';
        $regularExpression = (new RegularExpression())
            ->setPattern('[1-9]')
            ->endOfString();

        $result = $regularExpression->matches($subject);

        $this->assertTrue($result->isValid());
        $this->assertTrue($result->isSuccessful());
    }

    public function testWhitespace(): void
    {
        $subject = '1 8 7';
        $regularExpression = (new RegularExpression())
            ->whitespace();

        $result = $regularExpression->matches($subject);

        $this->assertTrue($result->isValid());
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals(1, $result->getCount());
        $this->assertEquals([' '], $result->getMatches());
        $this->assertEquals(0, $result->getFlags());
        $this->assertEquals($subject, $result->getSubject());
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param int $m
     * @param int|null $n
     * @param string $pattern
     *
     * @dataProvider dataRepeat
     */
    public function testRepeat(
        RegularExpression $regularExpression,
        string $subject,
        int $m,
        ?int $n,
        string $pattern
    ): void {
        $result = $regularExpression->repeat($m, $n)->matches($subject);

        $this->assertTrue($result->isValid());
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals($subject, $result->getSubject());
        $this->assertEquals($pattern, $result->getPattern());
    }

    /**
     * @return array
     */
    public function dataRepeat(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->digit(),
                '187',
                2,
                null,
                '/[0-9]{2}/'
            ],
            '#2' => [
                (new RegularExpression())->digit(),
                '187',
                1,
                3,
                '/[0-9]{1,3}/'
            ]
        ];
    }
}
