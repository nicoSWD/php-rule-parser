<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\methods;

use nicoSWD\Rules\tests\integration\AbstractTestBase;

class ConcatTest extends AbstractTestBase
{
    public function testAllParametersAreConcatenated()
    {
        $this->assertTrue($this->evaluate('foo.concat("bar", "baz") === "foobarbaz"', ['foo' => 'foo']));
        $this->assertTrue($this->evaluate('"foo".concat("bar", "baz") === "foobarbaz"'));
        $this->assertTrue($this->evaluate('"foo".concat() === "foo"'));
        $this->assertTrue($this->evaluate('"foo".concat("bar", 1) === "foobar1"'));
    }

    public function testArraysAreImplodedByCommaBeforeConcatenating()
    {
        $this->assertTrue($this->evaluate('"foo".concat("bar", [1, 2]) === "foobar1,2"'));
    }
}
