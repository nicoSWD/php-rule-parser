## PHP Rule Engine


[![Latest Stable Version](https://travis-ci.org/nicoSWD/php-rule-parser.svg?branch=master)](https://travis-ci.org/nicoSWD/php-rule-parser)
[![Build status][Master coverage image]][Master coverage] 
[![Code Quality][Master quality image]][Master quality] 
[![StyleCI](https://styleci.io/repos/39503126/shield?branch=master&style=flat)](https://styleci.io/repos/39503126)

[![Total Downloads](https://img.shields.io/packagist/dt/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser) 
[![Latest Stable Version](https://img.shields.io/packagist/v/nicoswd/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser)

You're looking at a standalone PHP library to parse and evaluate text based rules with a Javascript-like syntax. This project was born out of the necessity to evaluate hundreds of rules that were originally written and evaluated in JavaScript, and now needed to be evaluated on the server-side, using PHP.

This library has initially been used to change and configure the behavior of certain "Workflows" (without changing actual code) in an intranet application, but it may serve a purpose elsewhere.


Find me on Twitter: @[nicoSWD](https://twitter.com/nicoSWD)

(If you're using PHP 5, you might want to take a look at [version 0.4.0](https://github.com/nicoSWD/php-rule-parser/tree/0.4.0))

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/67203389-970c-419c-9430-a7f9a005bd94/big.png)](https://insight.sensiolabs.com/projects/67203389-970c-419c-9430-a7f9a005bd94)

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
$variables = ['foo' => 6];

$rule = new Rule('foo in [4, 6, 7]', $variables);
var_dump($rule->isTrue()); // bool(true)
```

Simple array manipulation 
```php
$rule = new Rule('[1, 4, 3].join(".") === "1.4.3"');
var_dump($rule->isTrue()); // bool(true)
```

Test if a value is between a given range
```php
$variables = ['threshold' => 80];

$rule = new Rule('threshold >= 50 && threshold <= 100', $variables);
var_dump($rule->isTrue()); // bool(true)
```

Call methods on objects from within rules
```php
class User
{
    // ...

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

Name        | Example             
----------- | ------------------------
charAt      | `"foo".charAt(2) === "o"`            
concat      | `"foo".concat("bar", "baz") === "foobarbaz"`
endsWith    | `"foo".endsWith("oo") === true`               
startsWith  | `"foo".startsWith("fo") === true`        
indexOf     | `"foo".indexOf("oo") === 1`                
join        | `["foo", "bar"].join(",") === "foo,bar"`            
replace     | `"foo".replace("oo", "aa") === "faa"`               
split       | `"foo-bar".split("-") === ["foo", "bar"]`           
substr      | `"foo".substr(1) === "oo"`                 
test        | `"foo".test(/oo$/) === true`                     
toLowerCase | `"FOO".toLowerCase() === "foo"`                      
toUpperCase | `"foo".toUpperCase() === "FOO"`                      

## Built-in Functions

Name        | Example             
----------- | ------------------------
parseInt    | `parseInt("22aa") === 22`            
parseFloat  | `parseFloat("3.1") === 3.1`

## Supported Operators

Type        | Description              | Operator
----------- | ------------------------ | ----------
Comparison  | greater than             | >
Comparison  | greater than or equal to | >=
Comparison  | less than                | <
Comparison  | less or equal to         | <=
Comparison  | equal to                 | ==
Comparison  | not equal to             | !=
Comparison  | identical                | ===
Comparison  | not identical            | !==
Containment | contains                 | in 
Containment | does not contain         | not in 
Logical     | and                      | &&
Logical     | or                       | \|\|

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
use nicoSWD\Rule;

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

$highlighter = new Rule\Highlighter\Highlighter(new Rule\Tokenizer());

// Optional custom styles
$highlighter->setStyle(
    Rule\Constants::GROUP_VARIABLE,
    'color: #007694; font-weight: 900;'
);

echo $highlighter->highlightString($ruleStr);
```

Outputs:

![Syntax preview](https://s3.amazonaws.com/f.cl.ly/items/0y1b0s0J2v2v1u3O1F3M/Screen%20Shot%202015-08-05%20at%2012.15.21.png)

## Notes

- Parentheses can be nested, and will be evaluated from right to left.
- Only value/variable comparison expressions with optional logical ANDs/ORs, are supported.

## Security

If you discover any security related issues, please email security@nic0.me instead of using the issue tracker.

## Testing

```shell
$ composer test
```

## Contributing

Pull requests are very welcome! If they include tests, even better. This project follows PSR-2 coding standards, please make sure your pull requests do too.

## To Do

- Support for object properties (foo.length)
- Support for returning actual results, other than true or false
- Support for array / string dereferencing: "foo"[1]
- Don't force boolean comparison for tokens that are already booleans. `my_func() && 2 > 1` should work
- Change regex and implementation for method calls. ".split(" should not be the token
- Add / implement missing methods
- Add "typeof" construct
- Do math (?)
- Allow string concatenating with "+"
- Invalid regex modifiers should not result in an unknown token
- Duplicate regex modifiers should throw an error
- ~~Add support for function calls~~
- ~~Support for regular expressions~~
- ~~Fix build on PHP 7 / Nightly~~
- ~~Allow variables in arrays~~
- ~~Verify function and method name spelling (.tOuPpErCAse() is currently valid)~~
- ...

## License

[![License](https://img.shields.io/packagist/l/nicoSWD/php-rule-parser.svg)](https://packagist.org/packages/nicoswd/php-rule-parser)

  [Master]: https://github.com/nicoSWD/php-rule-parser/tree/master
  [Master coverage image]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/badges/coverage.png?b=master
  [Master coverage]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master
  [Master quality image]: https://img.shields.io/scrutinizer/g/nicoswd/php-rule-parser.svg?b=master
  [Master quality]: https://scrutinizer-ci.com/g/nicoSWD/php-rule-parser/?branch=master
