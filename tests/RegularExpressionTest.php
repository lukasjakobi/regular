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
     * @param bool $successful
     *
     * @dataProvider dataMatches
     */
    public function testMatches(RegularExpression $regularExpression, string $subject, int $count, array $matches, bool $successful): void
    {
        $result = $regularExpression->matches($subject);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals($matches, $result->getMatches());
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
                true
            ],
            '#2' => [
                (new RegularExpression())->between(6, 9),
                'my favourite numbers are 7 and 9',
                1,
                ['7'],
                true
            ],
            '#3' => [
                (new RegularExpression())->between(6, 9),
                'no matches',
                0,
                [],
                false
            ]
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param int $count
     * @param array $matches
     * @param bool $successful
     *
     * @dataProvider dataMatchesAll
     */
    public function testMatchesAll(RegularExpression $regularExpression, string $subject, int $count, array $matches, bool $successful): void
    {
        $result = $regularExpression->matchesAll($subject);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals($matches, $result->getMatches());
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
                true
            ],
            '#2' => [
                (new RegularExpression())->between(6, 9),
                'my favourite numbers are 7 and 9',
                2,
                ['7', '9'],
                true
            ],
            '#3' => [
                (new RegularExpression())->between(6, 9),
                'no matches',
                0,
                [],
                false
            ]
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string|array $replacement
     * @param string|array $subject
     * @param int $count
     * @param string|array $results
     * @param bool $successful
     *
     * @dataProvider dataReplace
     */
    public function testReplace(
        RegularExpression $regularExpression,
        string|array $replacement,
        string|array $subject,
        string|array $results,
        int $count,
        bool $successful
    ): void {
        $result = $regularExpression->replace($replacement, $subject);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals($results, $result->getResponse());
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
                1,
                true
            ],
            '#2' => [
                (new RegularExpression())->char('\.'),
                ' ',
                'this.text.is.dotted',
                'this text is dotted',
                3,
                true
            ],
            '#3' => [
                (new RegularExpression())->char('x'),
                'replacement',
                'no matches',
                'no matches',
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
     * @param bool $successful
     *
     * @dataProvider dataGrep
     */
    public function testGrep(
        RegularExpression $regularExpression,
        array $array,
        array $response,
        int $count,
        bool $successful
    ): void {
        $result = $regularExpression->grep($array);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($count, $result->getCount());
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
                true
            ],
            '#2' => [
                (new RegularExpression())->char('x'),
                ['x', 'xxx', 'no'],
                ['x', 'xxx'],
                2,
                true
            ],
            '#3' => [
                (new RegularExpression())->char('x'),
                ['a', 'b', 'c'],
                [],
                0,
                false
            ],
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param array $results
     * @param int $count
     * @param bool $successful
     *
     * @dataProvider dataSplit
     */
    public function testSplit(
        RegularExpression $regularExpression,
        string $subject,
        array $results,
        int $count,
        bool $successful
    ): void {
        $result = $regularExpression->split($subject);

        $this->assertTrue($result->isValid());
        $this->assertEquals($successful, $result->isSuccessful());
        $this->assertEquals($count, $result->getCount());
        $this->assertEquals($results, $result->getResponse());
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
                3,
                true
            ],
            '#2' => [
                (new RegularExpression())->char('d'),
                'derdiedas',
                ['', 'er', 'ie', 'as'],
                4,
                true
            ],
            '#3' => [
                (new RegularExpression())->char('x'),
                'no match',
                ['no match'],
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
}
