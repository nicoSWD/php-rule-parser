## Rules Parser and Evaluator for PHP 5.4+

[![Build Status](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/build.png?b=develop)](https://travis-ci.org/nicoSWD/php-rule-parser) [![Code Coverage](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=develop) [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/nicoswd/php-rule-parser.svg?b=develop)](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=develop) [![HHVM tested](https://img.shields.io/hhvm/nicoswd/php-rule-parser.svg)](https://travis-ci.org/nicoSWD/php-rule-parser) [![Latest Stable Version](https://img.shields.io/packagist/v/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser)

You're looking at a PHP library to parse and evaluate text based rules with a Javascript-like syntax. This project was born out of the necessity to evaluate hundreds of rules that were originally written and evaluated in JavaScript, and now needed to be evaluated on the server-side, using PHP.

This library has initially been used to change and configure the behavior of certain "Workflows" (without changing actual code) in an intranet application, but it may serve a purpose elsewhere.


Find me on Twitter: @[nicoSWD](https://twitter.com/nicoSWD)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/67203389-970c-419c-9430-a7f9a005bd94/big.png)](https://insight.sensiolabs.com/projects/67203389-970c-419c-9430-a7f9a005bd94)

## Install

Via Composer

``` bash
$ composer require "nicoswd/php-rule-parser": "0.3.*"
```

Via git
``` bash
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
    foo.toUpperCase() == "ABC" && (
        bar == 123 || bar >= 321
    ) && foo in ["abc", "def", "ghi"]';

$variables = [
    'foo' => 'abc',
    'bar' => 321,
    'baz' => 123
];

$rule = new Rule($ruleStr, $variables);

var_dump($rule->isTrue()); // bool(true)
```

Standard JavaScript code comments are supported.

```php
$ruleStr = '
    /**
     * This is a test rule with comments
     */

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

$variables = [
    'foo' => 5,
    'bar' => 7
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

## Supported Comparison Operators
- Equal: `=`, `==`, `===` (strict), `is`
- Not equal: `!=`, `!==` (strict), `is not`
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

``` bash
$ phpunit
```

## Contributing
Pull requests are very welcome! If they include tests, even better. This project follows PSR-2 coding standards, please make sure your pull requests do too.

## License

[![License](https://img.shields.io/packagist/l/nicoSWD/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rules-parser)
