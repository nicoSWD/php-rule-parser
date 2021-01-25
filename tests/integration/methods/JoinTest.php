<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class JoinTest extends AbstractTestBase
{
    protected array $array = ['foo' => ['foo', 'bar']];

    /** @test */
    public function ifOmittedDelimiterFallsBackToDefault(): void
    {
        $this->assertTrue($this->evaluate('foo.join() === "foo,bar"', $this->array));
    }

    /** @test */
    public function literalStringDelimiter(): void
    {
        $this->assertTrue($this->evaluate('foo.join("|") === "foo|bar"', $this->array));
    }

    /** @test */
    public function variableValueAsDelimiter(): void
    {
        $this->assertTrue($this->evaluate(
            'foo.join(separator) === "foo$bar"',
            $this->array + ['separator' => '$']
        ));
    }

    /** @test */
    public function callOnStringLiteralArray(): void
    {
        $this->assertTrue($this->evaluate('[1, 2, 3].join("|") === "1|2|3"'));
        $this->assertTrue($this->evaluate('[1, 2, 3] . join("|") === "1|2|3"'));
    }

    /** @test */
    public function variableInArrayIsJoined(): void
    {
        $this->assertTrue($this->evaluate('[1, 2, foo].join("|") === "1|2|3"', ['foo' => 3]));
    }

    /** @test */
    public function joinOnEmptyArray(): void
    {
        $this->assertTrue($this->evaluate('[].join("|") === ""'));
    }
}
