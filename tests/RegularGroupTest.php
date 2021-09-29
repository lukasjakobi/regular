<?php

declare(strict_types=1);

namespace LukasJakobi\Regular\Test;

use LukasJakobi\Regular\RegularExpression;
use LukasJakobi\Regular\RegularGroup;
use PHPUnit\Framework\TestCase;

class RegularGroupTest extends TestCase
{
    /**
     * @param RegularExpression $regularExpression
     * @param RegularGroup $regularGroup
     * @param string $expression
     *
     * @dataProvider dataGroup
     */
    public function testGroup(
        RegularExpression $regularExpression,
        RegularGroup $regularGroup,
        string $expression
    ): void {
        $regularExpression->group($regularGroup);

        $this->assertEquals($expression, $regularExpression->toExpression());
    }

    public function dataGroup()
    {
        return [
            '#1' => [
                (new RegularExpression())->add('[1-9]'),
                (new RegularGroup())->char('z')->digit(),
                '/[1-9](z[0-9])/'
            ],
        ];
    }
}
