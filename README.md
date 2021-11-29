```asciidoc
______                 _              _____                             _                 
| ___ \               | |            |  ___|                           (_)                
| |_/ /___  __ _ _   _| | __ _ _ __  | |____  ___ __  _ __ ___  ___ ___ _  ___  _ __  ___ 
|    // _ \/ _` | | | | |/ _` | "__| |  __\ \/ / "_ \| "__/ _ \/ __/ __| |/ _ \| "_ \/ __|
| |\ \  __/ (_| | |_| | | (_| | |    | |___>  <| |_) | | |  __/\__ \__ \ | (_) | | | \__ \
\_| \_\___|\__, |\__,_|_|\__,_|_|    \____/_/\_\ .__/|_|  \___||___/___/_|\___/|_| |_|___/
            __/ |                              | |                                        
           |___/                               |_|                                                                      
```
## Regular - PHP Regex Builder & preg_* Interface

[![version](https://shields.io/github/v/release/lukasjakobi/regular?include_prereleases&color=217FA4)](https://github.com/lukasjakobi/regular/releases)
[![php](https://shields.io/github/languages/top/lukasjakobi/regular?color=2A8EA6)]()
[![php-v](https://shields.io/packagist/php-v/lukasjakobi/regular?color=339DA9)]()
[![licence](https://shields.io/github/license/lukasjakobi/regular?color=44BBAD)]()

## Installation

```bash
composer require lukasjakobi/regular
```

Or download [the latest release](https://github.com/lukasjakobi/regular/releases/latest).


## Documentation

https://github.com/lukasjakobi/regular/wiki

### Installation Guide

https://github.com/lukasjakobi/regular/wiki/Installation

# Examples

## Match (preg_match)
[![licence](https://img.shields.io/static/v1?label=&message=complete&color=4DCAAF)]()

Check whether your pattern matches the subject

### Telephone Number

```php
use LukasJakobi\Regular\RegularExpression;

$regular = (new RegularExpression())
    ->addChars("+")
    ->addAnyDigit()
    ->repeatBetween(1, 3)
    ->addWhitespace()
    ->addAnyDigit()
    ->repeatBetween(4, 14);

echo $regular->toExpression();
echo $regular->matches("+49 123456789")->isValid();
```

#### Output
```regexp
/+\d{1,3}\s\d{4,14}/
```
```
true
```

### Mysterious Band Name

```php
use LukasJakobi\Regular\RegularExpression;
use LukasJakobi\Regular\RegularModifier;

$regular = (new RegularExpression())
    ->setModifiers([RegularModifier::INSENSITIVE, RegularModifier::GLOBAL])
    ->addDigitBetween(1, 8)
    ->repeatExactly(3)
    ->addWhitespace()
    ->addCustom("Straßenbande");

echo $regular->toExpression();
```

#### Output
```regexp
/[1-8]{3}\sStraßenbande/ig
```

### Username

```php
use LukasJakobi\Regular\RegularExpression;

$regular = (new RegularExpression())
    ->startOfString()
    ->addCustom('[a-zA-Z0-9_-]') // your custom rules for username
    ->repeatBetween(3, 16) // the length of the username
    ->endOfString();
    
echo $regular->toExpression();
echo $regular->matches("Steve")->isValid();
```

#### Output
```regexp
/^[a-zA-Z0-9_-]{3,16}$/
```
```
true
```

## Replace (preg_replace)
[![licence](https://img.shields.io/static/v1?label=&message=complete&color=4DCAAF)]()

Replace texts

```php
use LukasJakobi\Regular\RegularExpression;
use LukasJakobi\Regular\RegularGroup;

$subject = ["13.10.2021", "24.12.1990", "09.10.2000"];

$group = new RegularGroup();
$group->addAnyDigit()->repeatBetween(2, 4);

$regular = (new RegularExpression())
    ->addCapturingGroup($group)
    ->addCapturingGroup($group)
    ->addCapturingGroup($group);

echo $regular->replace("$3-$2-$1", $subject);
```

#### Output
```json
["2021-10-13", "1990-12-24", "2000-12-24"]
```

## Split (preg_split)
[![licence](https://img.shields.io/static/v1?label=&message=complete&color=4DCAAF)]()


Split input string at pattern

```php
use LukasJakobi\Regular\RegularExpression;

$subject = "this_text_will_be_converted";
$regular = (new RegularExpression())
    ->addChars("_");

echo $regular->split($subject);
```

#### Output
```json
["this", "text", "will", "be", "converted"]
```

## Grep (preg_grep)
[![licence](https://img.shields.io/static/v1?label=&message=complete&color=4DCAAF)]()

Greps strings out of an array, that match your pattern

```php
use LukasJakobi\Regular\RegularExpression;

$subject = ["i am home", "are you home", "yes i am"];
$regular = (new RegularExpression())
    ->addCustom("home");

echo $regular->grep($subject);
```

#### Output
```json
["i am home", "are you home"]
```