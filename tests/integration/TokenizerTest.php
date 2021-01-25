<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Tokenizer\Tokenizer;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class TokenizerTest extends TestCase
{
    private Tokenizer $tokenizer;

    protected function setUp(): void
    {
        $this->tokenizer = new Tokenizer(new JavaScript(), new TokenFactory());
    }

    /** @test */
    public function getMatchedTokenReturnsFalseOnFailure(): void
    {
        $reflection = new ReflectionMethod($this->tokenizer, 'getMatchedToken');
        $reflection->setAccessible(true);
        $result = $reflection->invoke($this->tokenizer, []);

        $this->assertSame('Unknown', $result);
    }

    /** @test */
    public function tokenPositionAndLineAreCorrect(): void
    {
        $tokens = $this->tokenizer->tokenize('1');
        $tokens->rewind();

        $this->assertEquals(0, $tokens->current()->getOffset());
    }
}
