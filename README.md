## Rules Parser and Evaluator for PHP 5.4+

[![Build Status](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/build.png?b=master)](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master) [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/nicoswd/php-rule-parser.svg?b=master)](https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master) [![Latest Stable Version](https://img.shields.io/packagist/v/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser)

You're looking at a PHP library to parse and evaluate text based rules. This project was born out of the necessity to evaluate hundreds of rules that were originally written and evaluated in JavaScript, and now needed to be evaluated on the server-side, using PHP.

This library has initially been used to change and configure the behavior of certain "Workflows" (without changing actual code) in an intranet application, but it may serve a purpose elsewhere.


Find me on Twitter: @[nicoSWD](https://twitter.com/nicoSWD)

## Install

Via Composer

``` bash
$ composer require "nicoswd/php-rules-parser": "0.3.*"
```

Via git
``` bash
$ git clone git@github.com:nicoSWD/php-rule-parser.git
```


## Usage

```php
$ruleStr = 'foo == "abc" && (bar == 123 || bar == 321)';

$variables = [
    'foo' => 'abc',
    'bar' => 321
];

$rule = new Rule($ruleStr, $variables);

var_dump($rule->isTrue()); // bool(true)
```

It supports JavaScript syntax, as well as a custom syntax for an easier, more user friendly usage.

```php
$ruleStr = 'foo is "abc" and (bar is 123 or bar is 321)';
```

Standard JavaScript code comments are supported, as well as PHP-style `#` comments.

```php
$ruleStr = '
    /**
     * This is a test rule with comments
     */

    // This is true
    2 < 3 and (
        # this is false, because foo does not equal 4
        foo is 4
        # but bar is greater than 6
        or bar > 6
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
    (2 is 2) and (
        1 < 3 and 3 == 2 ( // Missing and/or before parentheses
            1 is 1
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
/**
 * This is a test rule with comments
 */

// This is true
2 < 3 and (
    # this is false, because foo does not equal 4
    foo is 4
    # but bar is greater than 6
    or bar > 6
)';

$highlighter = new Rules\Highlighter(new Rules\Tokenizer());

// Optional custom styles
// $highlighter->setStyle(
//    Rules\Constants::GROUP_VARIABLE,
//    'color: #007694; font-weight: 900;'
// );

echo $highlighter->highlightString($ruleStr);
```

Outputs:

![Syntax preview](https://s3.amazonaws.com/f.cl.ly/items/2U1j2T0M1q3U0D1t1t1D/Screen%20Shot%202015-07-22%20at%2016.51.47.png)

## Supported Comparison Operators
- Equal: `=`, `==`, `===`, `is`
- Not equal: `!=`, `!==`, `is not`
- Greater than: `>`
- Less than: `<`
- Greater than/equal: `>=`
- Less than/equal: `<=`

## Notes
- Variables are case-insensitive.
- Parentheses can be nested, and will be evaluated from right to left.
- Strict comparison operators (`===`, `!==`) are supported, but all values and variables will be treated as strings internally.
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