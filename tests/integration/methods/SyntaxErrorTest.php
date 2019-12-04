<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class SyntaxErrorTest extends AbstractTestBase
{
    /** @test */
    public function missingCommaInArgumentsThrowsException()
    {
        $rule = new Rule('"foo".charAt(1 2 ) === "b"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "2" at position 15', $rule->getError());
    }

    /** @test */
    public function missingValueInArgumentsThrowsException()
    {
        $rule = new Rule('"foo".charAt(1 , ) === "b"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "," at position 17', $rule->getError());
    }

    /** @test */
    public function missingValueBetweenCommasInArgumentsThrowsException()
    {
        $rule = new Rule('"foo".charAt(1 , , ) === "b"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "," at position 17', $rule->getError());
    }

    /** @test */
    public function unexpectedTokenInArgumentsThrowsException()
    {
        $rule = new Rule('"foo".charAt(1 , < , ) === "b"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "<" at position 17', $rule->getError());
    }

    /** @test */
    public function unexpectedEndOfStringThrowsException()
    {
        $rule = new Rule('"foo".charAt(1 , ');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected end of string', $rule->getError());
    }

    /** @test */
    public function undefinedMethodThrowsException()
    {
        $rule = new Rule('/^foo$/.teddst("foo") === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined method "teddst" at position 7', $rule->getError());
    }

    /** @test */
    public function incorrectSpellingThrowsException()
    {
        $rule = new Rule('"foo".ChARat(1) === "o"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined method "ChARat" at position 5', $rule->getError());
    }

    /** @test */
    public function callOnNonArray()
    {
        $rule = new Rule('"foo".join("|") === ""');

        $this->assertFalse($rule->isValid());
        $this->assertSame('foo.join is not a function', $rule->getError());
    }

    /** @test */
    public function exceptionIsThrownOnTypeError()
    {
        $rule = new Rule('"foo".test("foo") === false');

        $this->assertFalse($rule->isValid());
        $this->assertSame('undefined is not a function', $rule->getError());
    }
}
