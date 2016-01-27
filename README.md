## Rules Parser and Evaluator for PHP 5.4+

|  | Build Status | Code Quality | Coverage | HHVM |
|:----------------:|:----------------:|:----------:|:----------:|:----------:|
| [v0.3][0.3] | [![Build status][0.3 image]][0.3] | [![Code Quality][0.3 quality image]][0.3 quality] | [![Coverage Status][0.3 coverage image]][0.3 coverage] | [![HHVM Tested][0.3 hhvm image]][0.3 hhvm] |
| [Master][Master] | [![Build status][Master image]][Master] | [![Code Quality][Master quality image]][Master quality] | [![Build status][Master coverage image]][Master coverage] | [![HHVM Tested][Master hhvm image]][Master hhvm] |
| [Develop][Develop] | [![Build Status][Develop image]][Develop] | [![Code Quality][Develop quality image]][0.3 quality] | [![Coverage Status][Develop coverage image]][Develop coverage] | [![HHVM Tested][Develop hhvm image]][Develop hhvm] |

[![Latest Stable Version](https://img.shields.io/packagist/v/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser)

You're looking at a PHP library to parse and evaluate text based rules with a Javascript-like syntax. This project was born out of the necessity to evaluate hundreds of rules that were originally written and evaluated in JavaScript, and now needed to be evaluated on the server-side, using PHP.

This library has initially been used to change and configure the behavior of certain "Workflows" (without changing actual code) in an intranet application, but it may serve a purpose elsewhere.


Find me on Twitter: @[nicoSWD](https://twitter.com/nicoSWD)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/67203389-970c-419c-9430-a7f9a005bd94/big.png)](https://insight.sensiolabs.com/projects/67203389-970c-419c-9430-a7f9a005bd94)

## Install

Via Composer

```bash
$ composer require "nicoswd/php-rule-parser": "0.3.*"
```

Via git
```bash
$ git clone git@github.com:nicoSWD/php-rule-parser.git
```


## Usage

```php
use nicoSWD\Rules\Rule;

// Composer install
require '/path/to/vendor/autoload.php';
// Non-Composer install
require '/path/to/src/nicoSWD/Rules/Autoloader.php';

$ruleStr = '
    2 < 3 && (
        // False
        foo in [4, 6, 7] ||
        // True
        [1, 4, 3].join("") === "143" &&
        // True
        "bar" in "foo bar".split(" ") &&
        // True
        "foo bar".substr(4) === "bar"
    ) && (
        // True
        "foo|bar|baz".split("|") === ["foo", /* what */ "bar", "baz"] &&
        // True
        bar > 6
    )';

$variables = [
    'foo' => 'abc',
    'bar' => 321,
    'baz' => 123
];

$rule = new Rule($ruleStr, $variables);

var_dump($rule->isTrue()); // bool(true)
```

## Error Handling
Both, `$rule->isTrue()` and `$rule->isFalse()` will throw an exception if the syntax is invalid. These calls can either be placed inside a `try` / `catch` block, or it can be checked prior using `$rule->isValid()`.

```php
$ruleStr = '
    (2 == 2) && (
        1 < 3 && 3 == 2 ( // Missing and/or before parentheses
            1 == 1
        )
    )';

$rule = new Rule($ruleStr);

try {
    $rule->isTrue();
} catch (\Exception $e) {
    echo $e->getMessage();
}
```

Or alternatively:

```php
if (!$rule->isValid()) {
    echo $rule->getError();
}
```

Both will output: `Unexpected token "(" at position 25 on line 3`

## Syntax Highlighting
 
A custom syntax highlighter is also provided.

```php
use nicoSWD\Rules;

$ruleStr = '
    // This is true
    2 < 3 && (
        // This is false
        foo in [4, 6, 7] ||
        // True
        [1, 4, 3].join("") === "143"
    ) && (
        // True
        "foo|bar|baz".split("|" /* uh oh */) === ["foo", /* what */ "bar", "baz"] &&
        // True
        bar > 6
    )';

$highlighter = new Rules\Highlighter(new Rules\Tokenizer());

// Optional custom styles
$highlighter->setStyle(
   Rules\Constants::GROUP_VARIABLE,
   'color: #007694; font-weight: 900;'
);

echo $highlighter->highlightString($ruleStr);
```

Outputs:

![Syntax preview](https://s3.amazonaws.com/f.cl.ly/items/0y1b0s0J2v2v1u3O1F3M/Screen%20Shot%202015-08-05%20at%2012.15.21.png)

## Supported Operators
- Equal: `==`, `===` (strict)
- Not equal: `!=`, `!==` (strict)
- Greater than: `>`
- Less than: `<`
- Greater than/equal: `>=`
- Less than/equal: `<=`
- In: `in`

## Notes
- Parentheses can be nested, and will be evaluated from right to left.
- Only value/variable comparison expressions with optional logical ANDs/ORs, are supported. This is not a full JavaScript emulator.

## Security

If you discover any security related issues, please email security@nic0.me instead of using the issue tracker.

## Testing

```bash
$ composer test
```

## Contributing
Pull requests are very welcome! If they include tests, even better. This project follows PSR-2 coding standards, please make sure your pull requests do too.

## To Do
- Support for object properties (foo.length)
- Support for returning actual results, other than true or false
- Support for array / string dereferencing: "foo"[1]
- Change regex and implementation for method calls. ".split(" should not be the token
- Add / implement missing methods
- Add "typeof" construct
- Do math (?)
- Allow string concatenating with "+"
- Support for objects {} (?)
- Invalid regex modifiers should not result in an unknown token
- Duplicate regex modifiers should throw an error
- ~~Add support for function calls~~
- ~~Support for regular expressions~~
- ~~Fix build on PHP 7 / Nightly~~
- ~~Allow variables in arrays~~
- ~~Verify function and method name spelling (.tOuPpErCAse() is currently valid)~~
- ...

## License

[![License](https://img.shields.io/packagist/l/nicoSWD/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rules-parser)

  [Master image]: https://travis-ci.org/nicoSWD/php-rule-parser.svg?branch=master
  [Master]: https://github.com/nicoSWD/php-rule-parser/tree/master
  [Master coverage image]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/coverage.png?b=master
  [Master coverage]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master
  [Master quality image]: https://img.shields.io/scrutinizer/g/nicoswd/php-rule-parser.svg?b=master
  [Master quality]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master
  [Master hhvm image]: https://img.shields.io/hhvm/nicoswd/php-rule-parser.svg?b=master
  [Master hhvm]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master
  [0.3 image]: https://travis-ci.org/nicoSWD/php-rule-parser.svg?branch=v0.3
  [0.3]: https://github.com/nicoSWD/php-rule-parser/tree/v0.3
  [0.3 coverage image]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/coverage.png?b=v0.3
  [0.3 coverage]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=v0.3
  [0.3 quality image]: https://img.shields.io/scrutinizer/g/nicoswd/php-rule-parser.svg?b=v0.3
  [0.3 quality]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=v0.3
  [0.3 hhvm image]: https://img.shields.io/hhvm/nicoswd/php-rule-parser.svg?b=v0.3
  [0.3 hhvm]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=v0.3
  [Develop image]: https://travis-ci.org/nicoSWD/php-rule-parser.svg?branch=develop
  [Develop]: https://github.com/nicoSWD/php-rule-parser/tree/develop
  [Develop coverage image]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/coverage.png?b=develop
  [Develop coverage]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=develop
  [Develop quality image]: https://img.shields.io/scrutinizer/g/nicoswd/php-rule-parser.svg?b=develop
  [Develop quality]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=develop
  [Develop hhvm image]: https://img.shields.io/hhvm/nicoswd/php-rule-parser.svg?b=develop
  [Develop hhvm]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=develop
