<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\methods;

/**
 * Class IndexOfTest
 */
class IndexOfTest extends \AbstractTestBase
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
}
