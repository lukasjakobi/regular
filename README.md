```asciidoc
  _____                  _            
 |  __ \                | |           
 | |__) |___  __ _ _   _| | __ _ _ __ 
 |  _  // _ \/ _` | | | | |/ _` | '__|
 | | \ \  __/ (_| | |_| | | (_| | |   
 |_|  \_\___|\__, |\__,_|_|\__,_|_|   
              __/ |                   
             |___/                                                                  
```
## Regular - PHP Regex Builder & preg_* Interface

[![Travis](https://img.shields.io/travis/lukasjakobi/regular.svg?style=flat-square)](https://travis-ci.org/lukasjakobi/regular)
[![release](https://img.shields.io/github/release/lukasjakobi/regular.svg?style=flat-square)](https://github.com/lukasjakobi/regular/releases)

## Installation

```bash
composer install lukasjakobi/regular
```

Or download [the latest release](https://github.com/lukasjakobi/regular/releases/latest).


## Documentation

https://github.com/lukasjakobi/regular/wiki


## Examples

## Telephone Number
```php
use LukasJakobi\Regular\RegularExpression;

$regular = (new RegularExpression())
    ->char('+')
    ->digit()
    ->repeat(1, 3)
    ->whitespace()
    ->digit()
    ->repeat(4, 14);

echo $regular->matches('+49 1234 56789');
```

## Band Name

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

## Postcode

```php
use LukasJakobi\Regular\RegularExpression;

$regular = (new RegularExpression())
    ->digit()
    ->repeat(5);

echo $regular->toExpression();
echo $regular->matches('06258');
```

#### Output
```regexp
/[0-9]{5}/
```
```
true
```
