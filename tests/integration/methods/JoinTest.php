<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class JoinTest extends AbstractTestBase
{
    protected array $array = ['foo' => ['foo', 'bar']];

    #[Test]
    public function ifOmittedDelimiterFallsBackToDefault(): void
    {
        $this->assertTrue($this->evaluate('foo.join() === "foo,bar"', $this->array));
    }

    #[Test]
    public function literalStringDelimiter(): void
    {
        $this->assertTrue($this->evaluate('foo.join("|") === "foo|bar"', $this->array));
    }

    #[Test]
    public function variableValueAsDelimiter(): void
    {
        $this->assertTrue($this->evaluate(
            'foo.join(separator) === "foo$bar"',
            $this->array + ['separator' => '$']
        ));
    }

    #[Test]
    public function callOnStringLiteralArray(): void
    {
        $this->assertTrue($this->evaluate('[1, 2, 3].join("|") === "1|2|3"'));
        $this->assertTrue($this->evaluate('[1, 2, 3] . join("|") === "1|2|3"'));
    }

    #[Test]
    public function variableInArrayIsJoined(): void
    {
        $this->assertTrue($this->evaluate('[1, 2, foo].join("|") === "1|2|3"', ['foo' => 3]));
    }

    #[Test]
    public function joinOnEmptyArray(): void
    {
        $this->assertTrue($this->evaluate('[].join("|") === ""'));
    }
}
