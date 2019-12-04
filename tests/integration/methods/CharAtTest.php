<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class CharAtTest extends AbstractTestBase
{
    /** @test */
    public function ifOmittedPositionFallsBackToZero()
    {
        $this->assertTrue($this->evaluate('foo.charAt() === "b"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".charAt() === "b"'));
    }

    /** @test */
    public function callWithValidPosition()
    {
        $this->assertTrue($this->evaluate('foo.charAt(1) === "a"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".charAt(2) === "r"'));
    }

    /** @test */
    public function invalidOffsetReturnsEmptyString()
    {
        $this->assertTrue($this->evaluate('foo.charAt(99) === ""', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"foo".charAt(99) === ""'));
    }

    /** @test */
    public function ifBooleansAndNullAreCastedToOneAndZero()
    {
        $this->assertTrue($this->evaluate('"foo".charAt(true) === "o"'));
        $this->assertTrue($this->evaluate('"foo".charAt(false) === "f"'));
        $this->assertTrue($this->evaluate('"foo".charAt(null) === "f"'));
    }
}
