<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Tokenizer\Tokenizer;
use nicoSWD\Rule\TokenStream\Token\Token;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;

final class TokenizerTest extends TestCase
{
    private Tokenizer $tokenizer;

    protected function setUp(): void
    {
        $this->tokenizer = new Tokenizer(new JavaScript(), new TokenFactory());
    }

    #[Test]
    public function getMatchedTokenReturnsFalseOnFailure(): void
    {
        $reflection = new ReflectionMethod($this->tokenizer, 'getMatchedToken');
        $result = $reflection->invoke($this->tokenizer, []);

        $this->assertSame(Token::UNKNOWN, $result);
    }

    #[Test]
    public function tokenPositionAndLineAreCorrect(): void
    {
        $tokens = $this->tokenizer->tokenize('1');
        $tokens->rewind();

        $this->assertEquals(0, $tokens->current()->getOffset());
    }
}
