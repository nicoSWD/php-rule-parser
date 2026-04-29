## PHP Rule Engine

[![Latest Stable Version](https://img.shields.io/packagist/v/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser)
[![Total Downloads](https://img.shields.io/packagist/dt/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser) 
[![Build status][Master coverage image]][Master coverage] 
[![Code Quality][Master quality image]][Master quality] 
[![StyleCI](https://styleci.io/repos/39503126/shield?branch=master&style=flat)](https://styleci.io/repos/39503126)

A standalone PHP library to parse and evaluate text-based rules using a JavaScript-like syntax. This project was born out of the need to evaluate hundreds of rules originally written in JavaScript on the server side, using PHP.

The library was initially used to configure the behavior of "Workflows" in an intranet application without changing actual code, but it may serve a purpose elsewhere.

## Install

Via Composer

```bash
$ composer require nicoswd/php-rule-parser
```

## Bundles

This library works best with one of these bundles below, but they're not required

| Bundle   | Framework      | Packagist     |
| -------- |  ------------- | ------------- |
| [nicoSWD/rule-engine-bundle](https://github.com/nicoSWD/rule-engine-bundle) | Symfony | [![Latest Stable Version](https://img.shields.io/packagist/v/nicoswd/symfony-rule-engine-bundle.svg)](https://packagist.org/packages/nicoswd/symfony-rule-engine-bundle) |

## Usage Examples

Test if a value is in a given array
```php
$variables = [
    'coupon_code' => (string) $_POST['coupon_code'],
];

$rule = new Rule('coupon_code in ["summer_discount", "summer21"]', $variables);
var_dump($rule->isTrue()); // bool(true)
```

Performing a regular expression
```php
$variables = [
    'coupon_code' => (string) $_POST['coupon_code'],
];

$rule = new Rule('coupon_code.test(/^summer20[0-9]{2}$/)', $variables);
var_dump($rule->isTrue()); // bool(true)
```

Test if a value is between a given range
```php
$variables = ['points' => 80];

$rule = new Rule('points >= 50 && points <= 100', $variables);
var_dump($rule->isTrue()); // bool(true)
```

Perform arithmetic operations
```php
$variables = ['price' => 100, 'quantity' => 3];

$rule = new Rule('price * quantity > 250', $variables);
var_dump($rule->isTrue()); // bool(true)
```

Arithmetic operators follow standard precedence rules: `*`, `/`, `%` bind tighter than `+`, `-`. Parentheses can be used to override precedence.
```php
$rule = new Rule('2 + 3 * 4 == 14');
var_dump($rule->isTrue()); // bool(true) - multiplication before addition

$rule = new Rule('(2 + 3) * 4 == 20');
var_dump($rule->isTrue()); // bool(true) - parentheses override precedence
```

Unary minus and logical NOT operators are also supported:
```php
$rule = new Rule('-5 * 3 == -15');
var_dump($rule->isTrue()); // bool(true) - unary minus binds tighter than multiplication

$rule = new Rule('--5 == 5');
var_dump($rule->isTrue()); // bool(true) - double negation

$rule = new Rule('!false');
var_dump($rule->isTrue()); // bool(true) - logical NOT

$rule = new Rule('!(1 == 2)');
var_dump($rule->isTrue()); // bool(true) - NOT with parenthesized comparison

$rule = new Rule('!foo', ['foo' => false]);
var_dump($rule->isTrue()); // bool(true) - NOT with variable
```

Get the actual computed result of an expression (not just true/false)
```php
$rule = new Rule('5 * 3');
var_dump($rule->result()); // int(15)

$rule = new Rule('"hello " + "world"');
var_dump($rule->result()); // string("hello world")

$rule = new Rule('parseInt("42")');
var_dump($rule->result()); // int(42)

$rule = new Rule('price * quantity', ['price' => 100, 'quantity' => 3]);
var_dump($rule->result()); // int(300)

// Comparison and logical expressions still return bool
$rule = new Rule('foo > 5', ['foo' => 10]);
var_dump($rule->result()); // bool(true)
```

Call methods on objects from within rules
```php
class User
{
    public function points(): int
    {
        return 1337;    
    }
}

$variables = [
    'user' => new User(),
];

$rule = new Rule('user.points() > 300', $variables);
var_dump($rule->isTrue()); // bool(true)
```

For security reasons, PHP's magic methods like `__construct` and `__destruct` cannot be 
called from within rules. However, `__call` will be invoked automatically if available,
unless the called method is defined.

## Built-in Methods

| Name        | Example             |
| ----------- | ------------------------ |
| charAt      | `"foo".charAt(2) === "o"`            |
| concat      | `"foo".concat("bar", "baz") === "foobarbaz"` |
| endsWith    | `"foo".endsWith("oo")`               |
| startsWith  | `"foo".startsWith("fo")`        |
| indexOf     | `"foo".indexOf("oo") === 1`                |
| join        | `["foo", "bar"].join(",") === "foo,bar"`            |
| replace     | `"foo".replace("oo", "aa") === "faa"`               |
| split       | `"foo-bar".split("-") === ["foo", "bar"]`           |
| substr      | `"foo".substr(1) === "oo"`                 |
| test        | `"foo".test(/oo$/)`                     |
| toLowerCase | `"FOO".toLowerCase() === "foo"`                      |
| toUpperCase | `"foo".toUpperCase() === "FOO"`                      |

## Built-in Functions

| Name        | Example             |
| ----------- | ------------------------ |
| parseInt    | `parseInt("22aa") === 22`            |
| parseFloat  | `parseFloat("3.1") === 3.1` |

## Supported Operators

| Type        | Description              | Operator |
| ----------- | ------------------------ | ---------- |
| Comparison  | greater than             | > |
| Comparison  | greater than or equal to | >= |
| Comparison  | less than                | < |
| Comparison  | less or equal to         | <= |
| Comparison  | equal to                 | == |
| Comparison  | not equal to             | != |
| Comparison  | identical                | === |
| Comparison  | not identical            | !== |
| Containment | contains                 | in |
| Containment | does not contain         | not in |
| Logical     | and                      | && |
| Logical     | or                       | \|\| |
| Arithmetic  | addition                 | + |
| Arithmetic  | subtraction              | - |
| Arithmetic  | multiplication           | * |
| Arithmetic  | division                 | / |
| Arithmetic  | modulo                   | % |
| Unary       | negation                 | - |
| Unary       | logical NOT              | ! |

## Error Handling

Both `$rule->isTrue()` and `$rule->isFalse()` will throw an exception if the syntax is invalid. These calls can either be placed inside a `try` / `catch` block, or validity can be checked beforehand using `$rule->isValid()`.

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
    echo $rule->error;
}
```

Both will output: `Unexpected "(" at position 28`

## Syntax Highlighting

A custom syntax highlighter is also provided.

```php
use nicoSWD\Rule\Highlighter\Highlighter;
use nicoSWD\Rule\TokenStream\Token\TokenType;

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

$highlighter = new Highlighter();

// Optional custom styles
$highlighter->setStyle(
    TokenType::VARIABLE,
    'color: #007694; font-weight: 900;'
);

echo $highlighter->highlightString($ruleStr);
```

Outputs:

![Syntax preview](https://s3.amazonaws.com/f.cl.ly/items/0y1b0s0J2v2v1u3O1F3M/Screen%20Shot%202015-08-05%20at%2012.15.21.png)

## Security

If you discover any security related issues, please email security@nic0.me instead of using the issue tracker.

## Testing

```shell
$ composer test
```

## Contributing

Pull requests are very welcome! If they include tests, even better. This project follows [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards, please make sure your pull requests do too.

## To Do

- Support for object properties (foo.length)
- Support for array / string dereferencing: "foo"[1]
- ~~Support for returning actual results, other than true or false~~
- ~~Don't force boolean comparison for tokens that are already booleans. `my_func() && 2 > 1` should work~~
- ~~Allow string concatenating with "+"~~
- ~~Duplicate regex modifiers should throw an error~~
- ~~Add support for function calls~~
- ~~Support for regular expressions~~
- ~~Fix build on PHP 7 / Nightly~~
- ~~Allow variables in arrays~~
- ~~Verify function and method name spelling (.tOuPpErCAse() is currently valid)~~
- ~~Change regex and implementation for method calls~~
- ~~Add / implement missing methods~~
- ~~Invalid regex modifiers should not result in an unknown token~~
- ...

## License

[![License](https://img.shields.io/packagist/l/nicoSWD/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser)

  [Master]: https://github.com/nicoSWD/php-rule-parser/tree/master
  [Master coverage image]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/coverage.png?b=master
  [Master coverage]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master
  [Master quality image]: https://img.shields.io/scrutinizer/g/nicoswd/php-rule-parser.svg?b=master
  [Master quality]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master
