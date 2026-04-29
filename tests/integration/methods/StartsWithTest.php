<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class StartsWithTest extends AbstractTestBase
{
    #[Test]
    public function givenAStringWhenStartsWithNeedleItShouldReturnTrue(): void
    {
        $this->assertTrue($this->evaluate('"bar".startsWith("ba") === true'));
        $this->assertTrue($this->evaluate('foo.startsWith("ar", 1) === true', ['foo' => 'bar']));
    }

    #[Test]
    public function givenAStringWhenNotStartsWithNeedleItShouldReturnTrue(): void
    {
        $this->assertTrue($this->evaluate('"bar".startsWith("a") === false'));
        $this->assertTrue($this->evaluate('foo.startsWith("x") === false', ['foo' => 'bar']));
    }

    #[Test]
    public function givenAStringWhenTestedOnNonStringValuesItShouldThrowAnException(): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage('Call to undefined method "startsWith" on non-string');

        $this->evaluate('["hello"].startsWith() === false');
    }
}
