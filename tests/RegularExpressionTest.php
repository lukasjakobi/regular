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
                    ->addAnyDigit()
                    ->repeatExactly(5)
                    ->addWhitespace()
                    ->setModifiers([RegularModifier::MULTI_LINE, RegularModifier::INSENSITIVE]),
                '/\d{5}\s/mi'
            ],
            '#2' => [
                (new RegularExpression())
                    ->addChars('abc')
                    ->addCharsExcept('d')
                    ->repeatExactly(2)
                    ->endOfString(),
                '/[abc][^d]{2}$/'
            ],
            '#3' => [
                (new RegularExpression('^[^1-8]'))
                    ->addChars('abc')
                    ->addCharsExcept('b')
                    ->addCustom('.*')
                    ->setModifiers(RegularModifier::INSENSITIVE)
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
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals(($count > 0), $result->hasMatches());
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
                (new RegularExpression())->addDigitBetween(6, 9),
                'my favourite number is 7',
                1,
                ['7'],
                0,
                0,
                true
            ],
            '#2' => [
                (new RegularExpression())->addDigitBetween(6, 9),
                'my favourite numbers are 7 and 9',
                1,
                ['7'],
                0,
                0,
                true
            ],
            '#3' => [
                (new RegularExpression())->addDigitBetween(6, 9),
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
        $result = $regularExpression->matchesAll($subject, $flags, $offset);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals(($count > 0), $result->hasMatches());
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
                (new RegularExpression())->addDigitBetween(6, 9)->setDelimiter(RegularDelimiter::HASH),
                'my favourite number is 7',
                1,
                ['7'],
                0,
                0,
                true
            ],
            '#2' => [
                (new RegularExpression())->addDigitBetween(6, 9),
                'my favourite numbers are 7 and 9',
                2,
                ['7', '9'],
                0,
                0,
                true
            ],
            '#3' => [
                (new RegularExpression())->addDigitBetween(6, 9),
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
     *
     * @dataProvider dataReplace
     */
    public function testReplace(
        RegularExpression $regularExpression,
        string|array $replacement,
        string|array $subject,
        string|array $results,
        int $limit,
    ): void {
        $result = $regularExpression->replace($replacement, $subject, $limit);

        $this->assertEquals($results, $result);
    }

    /**
     * @return array
     */
    public function dataReplace(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->addChars('x'),
                '7',
                'my favourite number is x',
                'my favourite number is 7',
                -1,
            ],
            '#2' => [
                (new RegularExpression())->addChars('\.'),
                ' ',
                'this.text.is.dotted',
                'this text is.dotted',
                2,
            ],
            '#3' => [
                (new RegularExpression())->addChars('x'),
                'replacement',
                'no matches',
                'no matches',
                -1,
            ],
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param array $array
     * @param array $response
     * @param int $flags
     *
     * @dataProvider dataGrep
     */
    public function testGrep(
        RegularExpression $regularExpression,
        array $array,
        array $response,
        int $flags
    ): void {
        $result = $regularExpression->grep($array, $flags);

        $this->assertEquals($response, $result);
    }

    /**
     * @return array
     */
    public function dataGrep(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->addChars('a-z')->repeatAtLeast(1),
                ['a', 'b', 'cce', 'a1', '1a', '1'],
                ['a', 'b', 'cce', 'a1', '1a'],
                0,
            ],
            '#2' => [
                (new RegularExpression())->addChars('x'),
                ['x', 'xxx', 'no'],
                ['no'],
                1,
            ],
            '#3' => [
                (new RegularExpression())->addChars('x'),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                1,
            ],
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param array $results
     * @param int $limit
     * @param int $flags
     *
     * @dataProvider dataSplit
     */
    public function testSplit(
        RegularExpression $regularExpression,
        string $subject,
        array $results,
        int $limit,
        int $flags
    ): void {
        $result = $regularExpression->split($subject, $limit, $flags);

        $this->assertEquals($results, $result);
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
            ],
            '#2' => [
                (new RegularExpression())->char('d'),
                'derdiedas',
                ['', 'er', 'iedas'],
                3,
                0,
            ],
            '#3' => [
                (new RegularExpression())->char('x'),
                'no match',
                ['no match'],
                2,
                0,
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
}
