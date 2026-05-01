<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\unit\TokenStream;

use ArrayIterator;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenMethod;
use nicoSWD\Rule\TokenStream\Token\TokenString;
use nicoSWD\Rule\TokenStream\TokenIterator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TokenIteratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ArrayIterator|MockInterface $stack;
    private TokenIterator $tokenIterator;

    protected function setUp(): void
    {
        $this->stack = \Mockery::mock(ArrayIterator::class);

        $this->tokenIterator = new TokenIterator(
            $this->stack,
        );
    }

    #[Test]
    public function givenAStackWhenNotEmptyItShouldBeIterable()
    {
        $this->stack->shouldReceive('rewind');
        $this->stack->shouldReceive('valid')->andReturn(true, true, true, false);
        $this->stack->shouldReceive('key')->andReturn(1, 2, 3);
        $this->stack->shouldReceive('next');
        $this->stack->shouldReceive('current')->times(3)->andReturn(
            new TokenString('a'),
            new TokenMethod('.foo('),
            new TokenString('b')
        );

        foreach ($this->tokenIterator as $value) {
            $this->assertInstanceOf(BaseToken::class, $value);
        }
    }
}
