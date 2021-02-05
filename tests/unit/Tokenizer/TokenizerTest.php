<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\Tokenizer;

use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\Tokenizer\Tokenizer;
use PHPUnit\Framework\TestCase;

final class TokenizerTest extends TestCase
{
    /** @test */
    public function givenAGrammarWithCollidingRegexItShouldTakeThePriorityIntoAccount(): void
    {
        $tokens = $this->tokenizeWithGrammar('yes   somevar', [
            [Token\Token::BOOL_TRUE, '\byes\b', 20],
            [Token\Token::VARIABLE, '\b[a-z]+\b', 10],
            [Token\Token::SPACE, '\s+', 5],
        ]);

        $this->assertCount(3, $tokens);

        $this->assertTrue($tokens[0]->getValue());
        $this->assertSame(0, $tokens[0]->getOffset());
        $this->assertInstanceOf(Token\TokenBoolTrue::class, $tokens[0]);

        $this->assertSame('   ', $tokens[1]->getValue());
        $this->assertSame(3, $tokens[1]->getOffset());
        $this->assertInstanceOf(Token\TokenSpace::class, $tokens[1]);

        $this->assertSame('somevar', $tokens[2]->getValue());
        $this->assertSame(6, $tokens[2]->getOffset());
        $this->assertInstanceOf(Token\TokenVariable::class, $tokens[2]);
    }

    /** @test */
    public function givenAGrammarWithCollidingRegexWhenPriorityIsWrongItShouldNeverMatchTheOneWithLowerPriority(): void
    {
        $tokens = $this->tokenizeWithGrammar('somevar   yes', [
            [Token\Token::VARIABLE, '\b[a-z]+\b', 20],
            [Token\Token::BOOL_TRUE, '\byes\b', 10],
            [Token\Token::SPACE, '\s+', 5],
        ]);

        $this->assertCount(3, $tokens);

        $this->assertSame('somevar', $tokens[0]->getValue());
        $this->assertSame(0, $tokens[0]->getOffset());
        $this->assertInstanceOf(Token\TokenVariable::class, $tokens[0]);

        $this->assertSame('   ', $tokens[1]->getValue());
        $this->assertSame(7, $tokens[1]->getOffset());
        $this->assertInstanceOf(Token\TokenSpace::class, $tokens[1]);

        $this->assertSame('yes', $tokens[2]->getValue());
        $this->assertSame(10, $tokens[2]->getOffset());
        $this->assertInstanceOf(Token\TokenVariable::class, $tokens[2]);
    }

    /** @test */
    public function givenAGrammarItShouldBeAvailableThroughGetter(): void
    {
        $grammar = $this->getTokenizer([[Token\Token::BOOL_TRUE, '\byes\b', 10]])->getGrammar();

        $this->assertInstanceOf(Grammar::class, $grammar);
        $this->assertIsArray($grammar->getDefinition());
        $this->assertCount(1, $grammar->getDefinition());
    }

    /** @return Token\BaseToken[] */
    private function tokenizeWithGrammar(string $rule, array $definition): array
    {
        $stack = $this->getTokenizer($definition)->tokenize($rule);
        /** @var Token\BaseToken[] $tokens */
        $tokens = [];

        foreach ($stack as $token) {
            $tokens[] = $token;
        }

        return $tokens;
    }

    private function getTokenizer(array $definition): Tokenizer
    {
        $grammar = new class($definition) extends Grammar {
            private array $definition;

            public function __construct(array $definition)
            {
                $this->definition = $definition;
            }

            public function getDefinition(): array
            {
                return $this->definition;
            }
        };

        return new Tokenizer($grammar, new Token\TokenFactory());
    }
}
