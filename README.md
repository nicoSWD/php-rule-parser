## PHP Rule Engine

[![Latest Stable Version](https://img.shields.io/packagist/v/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser)
[![Total Downloads](https://img.shields.io/packagist/dt/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser) 
[![Build status][Master coverage image]][Master coverage] 
[![Code Quality][Master quality image]][Master quality] 
[![StyleCI](https://styleci.io/repos/39503126/shield?branch=master&style=flat)](https://styleci.io/repos/39503126)

A PHP library that parses and evaluates boolean expressions using a JavaScript-like syntax. It supports variables, comparison and logical operators, arithmetic, regular expressions, arrays, string methods, and function calls, all from plain text rules.

## Install

Via Composer

```bash
$ composer require nicoswd/php-rule-parser
```

## Usage Examples

### E-commerce: Validate coupon eligibility
```php
$variables = [
    'cart_total'     => 120,
    'user_tier'      => 'gold',
    'is_blacklisted' => false,
];

$rule = new Rule('cart_total >= 50 && user_tier in ["gold", "platinum"] && !is_blacklisted', $variables);
var_dump($rule->isTrue()); // bool(true) — eligible for discount
```

### Content moderation: Flag suspicious posts
```php
$variables = [
    'body'             => 'Check this out http://spam.com',
    'is_trusted_author' => false,
];

$rule = new Rule('body.test(/(https?:\/\/[^\s]+){3,}/) && !is_trusted_author', $variables);
var_dump($rule->isTrue()); // bool(true) — flagged as spam
```

### Access control: Check user permissions
```php
$variables = [
    'role'         => 'editor',
    'status'       => 'active',
    'is_suspended' => false,
];

$rule = new Rule('role in ["admin", "editor"] && status == "active" && !is_suspended', $variables);
var_dump($rule->isTrue()); // bool(true) — access granted
```

### Pricing: Calculate order total with conditions
```php
$variables = [
    'base_price' => 29.99,
    'tax_rate'   => 21,
    'quantity'   => 3,
];

$rule = new Rule('(base_price + (base_price * tax_rate / 100)) * quantity', $variables);
var_dump($rule->result()); // float(108.8571...) — total with tax
```

### Form validation: Check input constraints
```php
$variables = [
    'email'   => 'user@example.com',
    'age'     => 25,
    'country' => 'US',
];

$rule = new Rule('email.test(/^[^@\s]+@[^@\s]+\.[^@\s]+$/) && age >= 18 && country in ["US", "CA", "UK"]', $variables);
var_dump($rule->isTrue()); // bool(true) — valid registration
```

### Notification routing: Target specific users
```php
$variables = [
    'plan'                 => 'pro',
    'last_login'           => 3,
    'notification_opt_out' => false,
];

$rule = new Rule('plan in ["pro", "enterprise"] && last_login < 7 && !notification_opt_out', $variables);
var_dump($rule->isTrue()); // bool(true) — send notification
```

### Feature flags: Roll out features gradually
```php
$variables = [
    'user_id'     => 7,
    'environment' => 'production',
];

$rule = new Rule('user_id % 10 < 3 && environment == "production"', $variables);
var_dump($rule->isTrue()); // bool(true) — feature enabled for this user
```

### String manipulation: Format user data
```php
$variables = [
    'firstName' => 'John',
    'lastName'  => 'Doe',
];

$rule = new Rule('firstName.toUpperCase() + " " + lastName.toUpperCase()', $variables);
var_dump($rule->result()); // string("JOHN DOE")
```

### Object method calls: Evaluate complex conditions
```php
class Subscription
{
    public function isActive(): bool
    {
        return true;
    }

    public function daysUntilExpiry(): int
    {
        return 15;
    }
}

$variables = [
    'subscription' => new Subscription(),
];

$rule = new Rule('subscription.isActive() && subscription.daysUntilExpiry() > 7', $variables);
var_dump($rule->isTrue()); // bool(true) — subscription is active and not expiring soon
```

For security reasons, PHP's magic methods like __construct and __destruct cannot be 
called from within rules. However, __call will be invoked automatically if available,
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
