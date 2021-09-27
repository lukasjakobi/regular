<?php

declare(strict_types=1);

namespace Regex\Test;

use PHPUnit\Framework\TestCase;
use Regex\RegularDelimiter;
use Regex\RegularExpression;
use Regex\RegularGroup;
use Regex\RegularModifier;

class RegularExpressionTest extends TestCase
{

    public function testRegexBuilder(): void
    {
        $this->assertEquals('/[0-9]{5}/i', (new RegularExpression())->digit()->repeat(5)->toExpression());
        $this->assertEquals('/[a-zA-Z0-9]/w/q', (new RegularExpression())->pattern('[a-zA-Z0-9]/w')
            ->modifier(RegularModifier::LITERAL)->toExpression());
        $this->assertEquals('@[a-zA-Z0-9]/w@q', (new RegularExpression())->pattern('[a-zA-Z0-9]/w')
            ->modifier(RegularModifier::LITERAL)->delimiter(RegularDelimiter::AT)->toExpression());
    }

    public function testRegex(): void
    {
        $this->assertEquals('/[0-9]{5}/i', (new RegularExpression('[^a-e]'))->toExpression());
        $this->assertEquals('/[a-zA-Z0-9]/w/q', (new RegularExpression())->pattern('[a-zA-Z0-9]/w')
            ->modifier(RegularModifier::LITERAL)->toExpression());
        $this->assertEquals('@[a-zA-Z0-9]/w@q', (new RegularExpression())->pattern('[a-zA-Z0-9]/w')
            ->modifier(RegularModifier::LITERAL)->delimiter(RegularDelimiter::AT)->toExpression());
    }

    public function testGroup(): void
    {
        $group = (new RegularGroup())
            ->between(3, 7)
            ->add('[^e-v]');

        $this->assertEquals('/([3-7][^e-v]){4,6}/i', (new RegularExpression())->group($group)->repeat(4, 6)->toExpression());
    }
}