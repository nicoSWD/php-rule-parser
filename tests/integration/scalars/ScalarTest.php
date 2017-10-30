<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\tests\scalars;

use nicoSWD\Rules\tests\integration\AbstractTestBase;

class ScalarTest extends AbstractTestBase
{
    public function testBooleans()
    {
        $this->assertTrue($this->evaluate('"0" == false'));
        $this->assertFalse($this->evaluate('"0" === false'));
        $this->assertTrue($this->evaluate('1 == true'));
        $this->assertFalse($this->evaluate('1 === true'));
        $this->assertTrue($this->evaluate('foo == true', ['foo' => 'test']));
        $this->assertFalse($this->evaluate('foo === true', ['foo' => 'test']));
        $this->assertTrue($this->evaluate('foo === true', ['foo' => true]));
        $this->assertFalse($this->evaluate('foo === true', ['foo' => false]));
        $this->assertFalse($this->evaluate('foo !== true', ['foo' => true]));
    }

    public function testNullValues()
    {
        $this->assertTrue($this->evaluate('foo === null', ['foo' => null]));
        $this->assertTrue($this->evaluate('foo !== null', ['foo' => 0]));
        $this->assertTrue($this->evaluate('foo !== null', ['foo' => '']));
        $this->assertTrue($this->evaluate('foo !== null', ['foo' => false]));
        $this->assertTrue($this->evaluate('"" == null', ['foo' => null]));
        $this->assertFalse($this->evaluate('"" === null', ['foo' => null]));
    }

    public function testFloatPrecision()
    {
        $this->assertFalse($this->evaluate('foo === "1.0000034"', ['foo' => 1.0000034]));
        $this->assertFalse($this->evaluate('foo === 1.0000034', ['foo' => '1.0000034']));
        $this->assertTrue($this->evaluate('foo === 1.0000034', ['foo' => 1.0000034]));
        $this->assertTrue($this->evaluate('foo === -1.0000034', ['foo' => -1.0000034]));
        $this->assertTrue($this->evaluate('1.0000035 > 1.0000034'));
        $this->assertTrue($this->evaluate('2 > 1.0000034'));
    }

    public function testNegativeNumbers()
    {
        $rule = 'foo > -1 && foo < 1';

        $this->assertTrue($this->evaluate($rule, ['foo' => '0']));

        $rule = 'foo == -1';

        $this->assertTrue($this->evaluate($rule, ['foo' => -1]));
    }
}
