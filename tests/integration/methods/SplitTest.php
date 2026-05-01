<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class SplitTest extends AbstractTestBase
{
    protected array $var = ['foo' => 'bar,baz,foo'];

    #[Test]
    public function ifOmittedSeparatorFallsBackToDefault(): void
    {
        $this->assertTrue($this->evaluate('foo.split() === ["bar,baz,foo"]', $this->var));
        $this->assertTrue($this->evaluate('["bar,baz,foo"] === foo.split()', $this->var));
    }

    #[Test]
    public function splittingLiteralStringAndVariableString(): void
    {
        $this->assertTrue($this->evaluate('foo.split(",") === ["bar", "baz", "foo"]', $this->var));
        $this->assertTrue($this->evaluate('"bar,baz,foo".split(",") === ["bar", "baz", "foo"]'));
    }

    #[Test]
    public function booleansAndNullDoNotSplitAnywhere(): void
    {
        $this->assertTrue($this->evaluate('"foo".split(true) === ["foo"]'));
        $this->assertTrue($this->evaluate('"foo".split(false) === ["foo"]'));
        $this->assertTrue($this->evaluate('"foo".split(null) === ["foo"]'));
    }

    #[Test]
    public function splitDelimiterAsVariable(): void
    {
        $this->assertTrue($this->evaluate(
            'foo.split(delimiter) === ["bar", "baz", "foo"]',
            $this->var + ['delimiter' => ',']
        ));
    }

    #[Test]
    public function splitDelimiterAsVariableWithMethodCall(): void
    {
        $this->assertTrue($this->evaluate(
            'foo.split(delimiter.toUpperCase()) === ["bbb", "bbb", "bbb"]',
            [
                'foo'       => 'bbbAbbbAbbb',
                'delimiter' => 'a',
            ]
        ));
    }

    #[Test]
    public function splitWithRegularExpression(): void
    {
        $this->assertTrue($this->evaluate('"foo     bar".split(/\s+/) === ["foo", "bar"]'));
    }

    #[Test]
    public function splitWithRegexAndLimit(): void
    {
        $this->assertTrue($this->evaluate('"foo bar baz".split(/\s+/, 2) === ["foo", "bar baz"]'));
    }

    #[Test]
    public function splitWithLimit(): void
    {
        $this->assertTrue($this->evaluate('"foo bar baz".split(" ", 2) === ["foo", "bar baz"]'));
    }
}
