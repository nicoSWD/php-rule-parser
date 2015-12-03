<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\methods;

class SubstrTest extends \AbstractTestBase
{
    public function testSubstrReturnsCorrectPartOfString()
    {
        $this->assertTrue($this->evaluate('foo.substr(1, 2) === "ar"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".substr(0, 1) === "b"'));
    }

    public function testOutOfBoundsOffsetReturnsEmptyString()
    {
        $this->assertTrue($this->evaluate('"bar".substr(100) === ""'));
    }

    public function testOmittedParametersReturnsSameString()
    {
        $this->assertTrue($this->evaluate('"bar".substr() === "bar"'));
    }

    public function testNegativeOffsetReturnsEndOfString()
    {
        $this->assertTrue($this->evaluate('"bar".substr(-1) === "r"'));
        $this->assertTrue($this->evaluate('"bar".substr(-1, 2) === "r"'));
        $this->assertTrue($this->evaluate('"bar".substr(-2, 2) === "ar"'));
        $this->assertTrue($this->evaluate('"bar".substr(-2) === "ar"'));
    }
}
