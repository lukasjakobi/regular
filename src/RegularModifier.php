<?php

namespace LukasJakobi\Regular;

interface RegularModifier
{
    public const GLOBAL = 'g';
    public const MULTI_LINE = 'm';
    public const INSENSITIVE = 'i';
    public const EXTENDED = 'x';
    public const SINGLE_LINE = 's';
    public const UNICODE = 'u';
    public const UNGREEDY = 'U';
    public const ANCHORED = 'A';
    public const J_CHANGED = 'J';
    public const DOLLAR_END_ONLY = 'D';
    public const NONE = '';
}
