<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class SplitTest extends AbstractTestBase
{
    protected array $var = ['foo' => 'bar,baz,foo'];

    /** @test */
    public function ifOmittedSeparatorFallsBackToDefault(): void
    {
        $this->assertTrue($this->evaluate('foo.split() === ["bar,baz,foo"]', $this->var));
        $this->assertTrue($this->evaluate('["bar,baz,foo"] === foo.split()', $this->var));
    }

    /** @test */
    public function splittingLiteralStringAndVariableString(): void
    {
        $this->assertTrue($this->evaluate('foo.split(",") === ["bar", "baz", "foo"]', $this->var));
        $this->assertTrue($this->evaluate('"bar,baz,foo".split(",") === ["bar", "baz", "foo"]'));
    }

    /** @test */
    public function booleansAndNullDoNotSplitAnywhere(): void
    {
        $this->assertTrue($this->evaluate('"foo".split(true) === ["foo"]'));
        $this->assertTrue($this->evaluate('"foo".split(false) === ["foo"]'));
        $this->assertTrue($this->evaluate('"foo".split(null) === ["foo"]'));
    }

    /** @test */
    public function splitDelimiterAsVariable(): void
    {
        $this->assertTrue($this->evaluate(
            'foo.split(delimiter) === ["bar", "baz", "foo"]',
            $this->var + ['delimiter' => ',']
        ));
    }

    /** @test */
    public function splitDelimiterAsVariableWithMethodCall(): void
    {
        $this->assertTrue($this->evaluate(
            'foo.split(delimiter.toUpperCase()) === ["bbb", "bbb", "bbb"]',
            [
                'foo'       => 'bbbAbbbAbbb',
                'delimiter' => 'a'
            ]
        ));
    }

    /** @test */
    public function splitWithRegularExpression(): void
    {
        $this->assertTrue($this->evaluate('"foo     bar".split(/\s+/) === ["foo", "bar"]'));
    }

    /** @test */
    public function splitWithRegexAndLimit(): void
    {
        $this->assertTrue($this->evaluate('"foo bar baz".split(/\s+/, 2) === ["foo", "bar baz"]'));
    }

    /** @test */
    public function splitWithLimit(): void
    {
        $this->assertTrue($this->evaluate('"foo bar baz".split(" ", 2) === ["foo", "bar baz"]'));
    }
}
