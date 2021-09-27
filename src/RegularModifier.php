<?php

namespace Regex;

interface RegularModifier
{
    public const CASE_INSENSITIVE = 'i';
    public const CASE_SENSITIVE = 'c';
    public const FREE_SPACING = 'x';
    public const EXACT_SPACING = 't';
    public const SINGLE_LINE = 's';
    public const MULTI_LINE = 'm';
    public const EXPLICIT_CAPTURE = 'n';
    public const DUPLICATE_NAMED_GROUPS = 'J';
    public const UNGREEDY_QUANTIFIERS = 'U';
    public const UNIX_LINES = 'd';
    public const BRE = 'b';
    public const ERE = 'e';
    public const LITERAL = 'q';
    public const RESET = '^';
}