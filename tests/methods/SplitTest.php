<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\methods;

/**
 * Class SplitTest
 */
class SplitTest extends \AbstractTestBase
{
    protected $var = ['foo' => 'bar,baz,foo'];

    public function testIfOmittedSeparatorFallsBackToDefault()
    {
        $this->assertTrue($this->evaluate('foo.split() === ["bar,baz,foo"]', $this->var));
        $this->assertTrue($this->evaluate('["bar,baz,foo"] === foo.split()', $this->var));
    }

    public function testSplittingLiteralStringAndVariableString()
    {
        $this->assertTrue($this->evaluate('foo.split(",") === ["bar", "baz", "foo"]', $this->var));
        $this->assertTrue($this->evaluate('"bar,baz,foo".split(",") === ["bar", "baz", "foo"]'));
    }

    public function testBooleansAndNullDoNotSplitAnywhere()
    {
        $this->assertTrue($this->evaluate('"foo".split(true) === ["foo"]'));
        $this->assertTrue($this->evaluate('"foo".split(false) === ["foo"]'));
        $this->assertTrue($this->evaluate('"foo".split(null) === ["foo"]'));
    }

    public function testSplitDelimiterAsVariable()
    {
        $this->assertTrue($this->evaluate(
            'foo.split(delimiter) === ["bar", "baz", "foo"]',
            $this->var + ['delimiter' => ',']
        ));
    }

    public function testSplitDelimiterAsVariableWithMethodCall()
    {
        $this->assertTrue($this->evaluate(
            'foo.split(delimiter.toUpperCase()) === ["bbb", "bbb", "bbb"]',
            [
                'foo'       => 'bbbAbbbAbbb',
                'delimiter' => 'a'
            ]
        ));
    }

    public function testSplitWithRegularExpression()
    {
        $this->assertTrue($this->evaluate('"foo     bar".split(/\s+/) === ["foo", "bar"]'));
    }

    public function testSplitWithRegexAndLimit()
    {
        $this->assertTrue($this->evaluate('"foo bar baz".split(/\s+/, 2) === ["foo", "bar baz"]'));
    }

    public function testSplitWithLimit()
    {
        $this->assertTrue($this->evaluate('"foo bar baz".split(" ", 2) === ["foo", "bar baz"]'));
    }
}
