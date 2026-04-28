<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class SyntaxErrorTest extends AbstractTestBase
{
    #[Test]
    public function missingCommaInArgumentsThrowsException(): void
    {
        $rule = new Rule('"foo".charAt(1 2 ) === "b"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "2" at position 15', $rule->getError());
    }

    #[Test]
    public function missingValueInArgumentsThrowsException(): void
    {
        $rule = new Rule('"foo".charAt(1 , ) === "b"');

        $this->assertTrue($rule->isValid());
        $this->assertFalse($rule->isTrue());
    }

    #[Test]
    public function missingValueBetweenCommasInArgumentsThrowsException(): void
    {
        $rule = new Rule('"foo".charAt(1 , , ) === "b"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "," at position 17', $rule->getError());
    }

    #[Test]
    public function unexpectedTokenInArgumentsThrowsException(): void
    {
        $rule = new Rule('"foo".charAt(1 , < , ) === "b"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "<" at position 17', $rule->getError());
    }

    #[Test]
    public function unexpectedEndOfStringThrowsException(): void
    {
        $rule = new Rule('"foo".charAt(1 , ');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected end of string', $rule->getError());
    }

    #[Test]
    public function undefinedMethodThrowsException(): void
    {
        $rule = new Rule('/^foo$/.teddst("foo") === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined method "teddst" at position 7', $rule->getError());
    }

    #[Test]
    public function incorrectSpellingThrowsException(): void
    {
        $rule = new Rule('"foo".ChARat(1) === "o"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined method "ChARat" at position 5', $rule->getError());
    }

    #[Test]
    public function callOnNonArray(): void
    {
        $rule = new Rule('"foo".join("|") === ""');

        $this->assertFalse($rule->isValid());
        $this->assertSame('foo.join is not a function', $rule->getError());
    }

    #[Test]
    public function exceptionIsThrownOnTypeError(): void
    {
        $rule = new Rule('"foo".test("foo") === false');

        $this->assertFalse($rule->isValid());
        $this->assertSame('test() is not a function', $rule->getError());
    }
}
