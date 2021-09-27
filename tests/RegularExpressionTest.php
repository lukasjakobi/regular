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
    public function testRegexBuilder(): void
    {
        $this->assertEquals('/[0-9]{5}\s/', (new RegularExpression())->digit()->repeat(5)->whitespace()
            ->toExpression());
        $this->assertEquals('/[a-zA-Z0-9]\w/i', (new RegularExpression())->pattern('[a-zA-Z0-9]\w')
            ->modifier(RegularModifier::CASE_INSENSITIVE)->toExpression());
        $this->assertEquals('@[a-zA-Z0-9]\w@m', (new RegularExpression())->pattern('[a-zA-Z0-9]\w')
            ->modifier(RegularModifier::MULTI_LINE)->delimiter(RegularDelimiter::AT)->toExpression());
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

        $this->assertEquals('/\+[0-9]{1,3}\s[0-9]{4,10}/', $regular->toExpression());
        $this->assertTrue($regular->matches('+49 1234 56789'));
    }

    public function testMatchStart(): void
    {
        $regular = (new RegularExpression())
            ->startOfString()
            ->charset('hello');

        $this->assertEquals('/^hello/', $regular->toExpression());
        $this->assertTrue($regular->matches('hello world'));
    }

    public function testMatchEnd(): void
    {
        $regular = (new RegularExpression())
            ->charset('hello')
            ->endOfString();

        $this->assertEquals('/hello$/', $regular->toExpression());
        $this->assertTrue($regular->matches('world hello'));
    }
}