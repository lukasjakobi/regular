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
                (new RegularExpression())->digit()->repeat(5)->whitespace(),
                '/[0-9]{5}\s/'
            ],
            '#2' => [
                (new RegularExpression())->char('abc')->notChar('d')->repeat(2)->endOfString(),
                '/[abc][^d]{2}$/'
            ],
            '#3' => [
                (new RegularExpression('^[^1-8]'))->char('abc')->notChar('b')->add('.*')
                    ->modifier(RegularModifier::CASE_INSENSITIVE)->delimiter(RegularDelimiter::HASH),
                '#^[^1-8][abc][^b].*#i'
            ],
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param int $count
     * @param array $matches
     *
     * @dataProvider dataMatches
     */
    public function testMatches(RegularExpression $regularExpression, string $subject, int $count, array $matches): void
    {
        $regularResult = $regularExpression->matches($subject);

        $this->assertTrue($regularResult->valid());
        $this->assertEquals($count, $regularResult->count());
        $this->assertEquals($matches, $regularResult->matches());
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
                ['7']
            ],
            '#2' => [
                (new RegularExpression())->between(6, 9),
                'my favourite numbers are 7 and 9',
                1,
                ['7']
            ]
        ];
    }

    /**
     * @param RegularExpression $regularExpression
     * @param string $subject
     * @param int $count
     * @param array $matches
     *
     * @dataProvider dataMatchesAll
     */
    public function testMatchesAll(RegularExpression $regularExpression, string $subject, int $count, array $matches): void
    {
        $regularResult = $regularExpression->matchesAll($subject);

        $this->assertTrue($regularResult->valid());
        $this->assertEquals($count, $regularResult->count());
        $this->assertEquals($matches, $regularResult->matches());
    }

    /**
     * @return array
     */
    public function dataMatchesAll(): array
    {
        return [
            '#1' => [
                (new RegularExpression())->between(6, 9)->delimiter(RegularDelimiter::HASH),
                'my favourite number is 7',
                1,
                [['7']]
            ],
            '#2' => [
                (new RegularExpression())->between(6, 9),
                'my favourite numbers are 7 and 9',
                2,
                [['7', '9']]
            ]
        ];
    }

    public function testReplace(): void
    {
        $regular = (new RegularExpression())
            ->modifier(RegularModifier::CASE_INSENSITIVE)
            ->char('x');

        $regularResult = $regular->replace('replacement', 'this is the x');

        $this->assertEquals('/x/i', $regular->toExpression());
        $this->assertTrue($regularResult->valid());
        $this->assertEquals(1, $regularResult->count());
        $this->assertEquals('this is the replacement', $regularResult->matches());
    }

    public function testGrep(): void
    {
        $regular = (new RegularExpression())
            ->modifier(RegularModifier::CASE_INSENSITIVE)
            ->char('x');

        $regularResult = $regular->grep(['this is x', 'this is also x', 'this is not']);

        $this->assertEquals('/x/i', $regular->toExpression());
        $this->assertTrue($regularResult->valid());
        $this->assertEquals(2, $regularResult->count());
        $this->assertEquals(['this is x', 'this is also x'], $regularResult->matches());
    }

    public function testSplit(): void
    {
        $regular = (new RegularExpression())
            ->modifier(RegularModifier::CASE_INSENSITIVE)
            ->char('x');

        $regularResult = $regular->split('firstxsecondxthird');

        $this->assertEquals('/x/i', $regular->toExpression());
        $this->assertTrue($regularResult->valid());
        $this->assertEquals(3, $regularResult->count());
        $this->assertEquals(['first', 'second', 'third'], $regularResult->matches());
    }

    public function testQuote(): void
    {
        $result = (new RegularExpression())->quote('\s [1-8]');

        $this->assertEquals('\\\s \[1\-8\]', $result);
    }
}
