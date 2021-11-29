<?php

declare(strict_types=1);

namespace LukasJakobi\Regular;

class RegularError extends \Error
{
    public const DIGIT_RANGE_ERROR = "The digit has to be between 0 and 9";
    public const CHARACTER_LENGTH_ERROR = "A char has to be exactly character long";
}