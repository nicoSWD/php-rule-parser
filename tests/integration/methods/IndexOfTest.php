<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\methods;

use nicoSWD\Rules\tests\integration\AbstractTestBase;

class IndexOfTest extends AbstractTestBase
{
    public function testValidNeedleReturnsCorrectPosition()
    {
        $this->assertTrue($this->evaluate('foo.indexOf("a") === 1', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".indexOf("b") === 0'));
    }

    public function testOmittedParameterReturnsNegativeOne()
    {
        $this->assertTrue($this->evaluate('"bar".indexOf() === -1'));
    }

    public function testNegativeOneIsReturnedIfNeedleNotFound()
    {
        $this->assertTrue($this->evaluate('"bar".indexOf("foo") === -1'));
    }
}
