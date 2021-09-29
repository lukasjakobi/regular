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

[![Travis](https://img.shields.io/travis/lukasjakobi/regular.svg?style=flat-square)](https://travis-ci.org/lukasjakobi/regular)
[![release](https://img.shields.io/github/release/lukasjakobi/regular.svg?style=flat-square)](https://github.com/lukasjakobi/regular/releases)

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

Check whether your pattern matches the subject

### Telephone Number
```php
use LukasJakobi\Regular\RegularExpression;

$regular = (new RegularExpression())
    ->char("+")
    ->digit()
    ->repeat(1, 3)
    ->whitespace()
    ->digit()
    ->repeat(4, 14);

echo $regular->toExpression();
echo $regular->matches("+49 123456789")->isValid();
```

#### Output
```regexp
/+[0-9]{1,3}\s[0-9]{4,14}/
```
```
true
```

### Band Name

```php
use LukasJakobi\Regular\RegularExpression;
use LukasJakobi\Regular\RegularModifier;

$regular = (new RegularExpression())
    ->modifier(RegularModifier::CASE_INSENSITIVE)
    ->between(1, 8)
    ->repeat(3)
    ->whitespace()
    ->charset("Straßenbande");

echo $regular->toExpression();
```

#### Output
```regexp
/[1-8]{3}\sStraßenbande/i
```

### Postcode

```php
use LukasJakobi\Regular\RegularExpression;

$regular = (new RegularExpression())
    ->digit()
    ->repeat(5);

echo $regular->toExpression();
echo $regular->matches("06258")->isValid();
```

#### Output
```regexp
/[0-9]{5}/
```
```
true
```

## Replace (preg_replace)
Replace texts

```php
use LukasJakobi\Regular\RegularExpression;

$subject = "this_text_will_be_converted";
$regular = (new RegularExpression())
    ->char("_");

echo $regular->replace(" ", $subject)->getResponse();
```

#### Output
```
this text will be converted
```

## Split (preg_split)

Split input string at pattern

```php
use LukasJakobi\Regular\RegularExpression;

$subject = "this_text_will_be_converted";
$regular = (new RegularExpression())
    ->char("_");

echo $regular->split($subject)->getResponse();
```

#### Output
```json
["this", "text", "will", "be", "converted"]
```

## Grep (preg_grep)

Greps strings out of an array, that match your pattern

```php
use LukasJakobi\Regular\RegularExpression;

$subject = ["i am home", "are you home", "yes i am"];
$regular = (new RegularExpression())
    ->charset("home");

echo $regular->grep($subject)->getResponse();
```

#### Output
```json
["i am home", "are you home"]
```