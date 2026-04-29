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

final class CharAtTest extends AbstractTestBase
{
    #[Test]
    public function ifOmittedPositionFallsBackToZero(): void
    {
        $this->assertTrue($this->evaluate('foo.charAt() === "b"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".charAt() === "b"'));
    }

    #[Test]
    public function callWithValidPosition(): void
    {
        $this->assertTrue($this->evaluate('foo.charAt(1) === "a"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".charAt(2) === "r"'));
    }

    #[Test]
    public function invalidOffsetReturnsEmptyString(): void
    {
        $this->assertTrue($this->evaluate('foo.charAt(99) === ""', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"foo".charAt(99) === ""'));
    }

    #[Test]
    public function ifBooleansAndNullAreCastedToOneAndZero(): void
    {
        $this->assertTrue($this->evaluate('"foo".charAt(true) === "o"'));
        $this->assertTrue($this->evaluate('"foo".charAt(false) === "f"'));
        $this->assertTrue($this->evaluate('"foo".charAt(null) === "f"'));
    }
}
