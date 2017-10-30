<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\tests\methods;

use nicoSWD\Rules\tests\integration\AbstractTestBase;

class CharAtTest extends AbstractTestBase
{
    public function testIfOmittedPositionFallsBackToZero()
    {
        $this->assertTrue($this->evaluate('foo.charAt() === "b"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".charAt() === "b"'));
    }

    public function testCallWithValidPosition()
    {
        $this->assertTrue($this->evaluate('foo.charAt(1) === "a"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".charAt(2) === "r"'));
    }

    public function testInvalidOffsetReturnsEmptyString()
    {
        $this->assertTrue($this->evaluate('foo.charAt(99) === ""', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"foo".charAt(99) === ""'));
    }

    public function testIfBooleansAndNullAreCastedToOneAndZero()
    {
        $this->assertTrue($this->evaluate('"foo".charAt(true) === "o"'));
        $this->assertTrue($this->evaluate('"foo".charAt(false) === "f"'));
        $this->assertTrue($this->evaluate('"foo".charAt(null) === "f"'));
    }
}
