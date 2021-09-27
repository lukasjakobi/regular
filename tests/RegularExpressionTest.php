<?php

declare(strict_types=1);

namespace LukasJakobi\Regular\Test;

use LukasJakobi\Regular\RegularDelimiter;
use LukasJakobi\Regular\RegularExpression;
use LukasJakobi\Regular\RegularGroup;
use LukasJakobi\Regular\RegularModifier;
use PHPUnit\Framework\TestCase;

class RegularExpressionTest extends TestCase
{
    /**
     * @param RegularExpression $regularExpression
     * @param string $expected
     * @dataProvider dataRegexBuilder
     */
    public function testRegexBuilder(RegularExpression $regularExpression, string $expected): void
    {
        $this->assertEquals($expected, $regularExpression->toExpression());
    }

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

    public function testRegex(): void
    {
        $this->assertEquals('/[^a-e]/', (new RegularExpression('[^a-e]'))->toExpression());
        $this->assertEquals('´[^a-e]{3}´u', (new RegularExpression('[^a-e]{3}',
            RegularDelimiter::ACUTE_ACCENT, RegularModifier::UTF_8_ENCODED))->toExpression());
    }

    public function testChar(): void
    {
        $this->assertEquals('/a/', (new RegularExpression())->char('a')->toExpression());
        $this->assertEquals('/[a-z]/', (new RegularExpression())->char('a-z')->toExpression());
    }

    public function testNotChar(): void
    {
        $this->assertEquals('/[^a]/', (new RegularExpression())->notChar('a')->toExpression());
        $this->assertEquals('/[^a-z]/', (new RegularExpression())->notChar('a-z')->toExpression());
    }

    public function testGroup(): void
    {
        $group = (new RegularGroup())
            ->between(3, 7)
            ->add('[^e-v]');

        $this->assertEquals('/([3-7][^e-v]){4,6}/', (new RegularExpression())->group($group)
            ->repeat(4, 6)->toExpression());
    }

    public function testMatch(): void
    {
        $regular = (new RegularExpression())
            ->charset('\+')
            ->digit()
            ->repeat(1, 3)
            ->whitespace()
            ->digit()
            ->repeat(4, 10);

        $regularResult = $regular->matches('+49 123456789');

        $this->assertEquals('/\+[0-9]{1,3}\s[0-9]{4,10}/', $regular->toExpression());
        $this->assertTrue($regularResult->valid());
        $this->assertEquals(1, $regularResult->count());
        $this->assertEquals(['+49 123456789'], $regularResult->matches());
    }

    public function testMatchStart(): void
    {
        $regular = (new RegularExpression())
            ->startOfString()
            ->charset('hello');

        $this->assertEquals('/^hello/', $regular->toExpression());
        $this->assertTrue($regular->matches('hello world')->valid());
    }

    public function testMatchEnd(): void
    {
        $regular = (new RegularExpression())
            ->charset('world')
            ->endOfString();

        $this->assertEquals('/world$/', $regular->toExpression());
        $this->assertTrue($regular->matches('hello world')->valid());
    }
}
