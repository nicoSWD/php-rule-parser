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

final class SubstrTest extends AbstractTestBase
{
    #[Test]
    public function substrReturnsCorrectPartOfString(): void
    {
        $this->assertTrue($this->evaluate('foo.substr(1, 2) === "ar"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".substr(0, 1) === "b"'));
    }

    #[Test]
    public function outOfBoundsOffsetReturnsEmptyString(): void
    {
        $this->assertTrue($this->evaluate('"bar".substr(100) === ""'));
    }

    #[Test]
    public function omittedParametersReturnsSameString(): void
    {
        $this->assertTrue($this->evaluate('"bar".substr() === "bar"'));
    }

    #[Test]
    public function negativeOffsetReturnsEndOfString(): void
    {
        $this->assertTrue($this->evaluate('"bar".substr(-1) === "r"'));
        $this->assertTrue($this->evaluate('"bar".substr(-1, 2) === "r"'));
        $this->assertTrue($this->evaluate('"bar".substr(-2, 2) === "ar"'));
        $this->assertTrue($this->evaluate('"bar".substr(-2) === "ar"'));
    }
}
