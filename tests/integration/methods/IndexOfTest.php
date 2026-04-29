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

final class IndexOfTest extends AbstractTestBase
{
    #[Test]
    public function validNeedleReturnsCorrectPosition(): void
    {
        $this->assertTrue($this->evaluate('foo.indexOf("a") === 1', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".indexOf("b") === 0'));
    }

    #[Test]
    public function omittedParameterReturnsNegativeOne(): void
    {
        $this->assertTrue($this->evaluate('"bar".indexOf() === -1'));
    }

    #[Test]
    public function negativeOneIsReturnedIfNeedleNotFound(): void
    {
        $this->assertTrue($this->evaluate('"bar".indexOf("foo") === -1'));
    }
}
