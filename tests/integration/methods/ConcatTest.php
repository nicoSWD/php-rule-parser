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

final class ConcatTest extends AbstractTestBase
{
    #[Test]
    public function allParametersAreConcatenated(): void
    {
        $this->assertTrue($this->evaluate('foo.concat("bar", "baz") === "foobarbaz"', ['foo' => 'foo']));
        $this->assertTrue($this->evaluate('"foo".concat("bar", "baz") === "foobarbaz"'));
        $this->assertTrue($this->evaluate('"foo".concat() === "foo"'));
        $this->assertTrue($this->evaluate('"foo".concat("bar", 1) === "foobar1"'));
    }

    #[Test]
    public function arraysAreImplodedByCommaBeforeConcatenating(): void
    {
        $this->assertTrue($this->evaluate('"foo".concat("bar", [1, 2]) === "foobar1,2"'));
    }
}
