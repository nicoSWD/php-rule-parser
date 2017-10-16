<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\tests\methods;

class SyntaxErrorTest extends \AbstractTestBase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected value at position 15 on line 1
     */
    public function testMissingCommaInArgumentsThrowsException()
    {
        $this->evaluate('"foo".charAt(1 2 ) === "b"');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "," at position 17 on line 1
     */
    public function testMissingValueInArgumentsThrowsException()
    {
        $this->evaluate('"foo".charAt(1 , ) === "b"');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "," at position 17 on line 1
     */
    public function testMissingValueBetweenCommasInArgumentsThrowsException()
    {
        $this->evaluate('"foo".charAt(1 , , ) === "b"');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "<" at position 17 on line 1
     */
    public function testUnexpectedTokenInArgumentsThrowsException()
    {
        $this->evaluate('"foo".charAt(1 , < , ) === "b"');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected end of string
     */
    public function testUnexpectedEndOfStringThrowsException()
    {
        $this->evaluate('"foo".charAt(1 , ');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage undefined is not a function at position 7 on line 1
     */
    public function testUndefinedMethodThrowsException()
    {
        $this->evaluate('/^foo$/.teddst("foo") === true');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage undefined is not a function
     */
    public function testIncorrectSpellingThrowsException()
    {
        $this->evaluate('"foo".ChARat(1) === "o"');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage foo.join is not a function at position 0 on line 1
     */
    public function testCallOnNonArray()
    {
        $this->evaluate('"foo".join("|") === ""');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage undefined is not a function at position 0 on line 1
     */
    public function testExceptionIsThrownOnTypeError()
    {
        $this->evaluate('"foo".test("foo") === false');
    }
}
