<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class EndsWithTest extends AbstractTestBase
{
    #[Test]
    public function givenAStringWhenEndsWithNeedleItShouldReturnTrue(): void
    {
        $this->assertTrue($this->evaluate('foo.endsWith("llo") === true', ['foo' => 'hello']));
        $this->assertTrue($this->evaluate('"hello".endsWith("llo") === true'));
    }

    #[Test]
    public function givenAStringWhenNotEndsWithNeedleItShouldReturnFalse(): void
    {
        $this->assertTrue($this->evaluate('"hello".endsWith("ell") === false'));
    }

    #[Test]
    public function givenAStringWhenTestedWithEndsWithWithoutArgsItShouldReturnFalse(): void
    {
        $this->assertTrue($this->evaluate('"hello".endsWith() === false'));
    }

    #[Test]
    public function givenAStringWhenTestedOnNonStringValuesItShouldThrowAnException(): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage('Call to undefined method "endsWith" on non-string');

        $this->evaluate('["hello"].endsWith() === false');
    }
}
