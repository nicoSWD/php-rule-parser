<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\unit\Tokenizer;

use nicoSWD\Rules\Grammar\Grammar;
use nicoSWD\Rules\Tokens\Token;
use nicoSWD\Rules\Tokenizer\Tokenizer;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenBool;
use nicoSWD\Rules\Tokens\TokenFactory;
use nicoSWD\Rules\Tokens\TokenSpace;
use nicoSWD\Rules\Tokens\TokenVariable;
use PHPUnit\Framework\TestCase;

class TokenizerTest extends TestCase
{
    /** @test */
    public function givenAGrammarWithCollidingRegexItShouldTakeThePriorityIntoAccount()
    {
        $tokens = $this->tokenizeWithGrammar('yes   somevar', [
            [Token::BOOL, '\b(?:yes|no)\b', 20],
            [Token::VARIABLE, '\b[a-z]+\b', 10],
            [Token::SPACE, '\s+', 5],
        ]);

        $this->assertCount(3, $tokens);

        $this->assertSame('yes', $tokens[0]->getOriginalValue());
        $this->assertSame(0, $tokens[0]->getOffset());
        $this->assertInstanceOf(TokenBool::class, $tokens[0]);

        $this->assertSame('   ', $tokens[1]->getValue());
        $this->assertSame(3, $tokens[1]->getOffset());
        $this->assertInstanceOf(TokenSpace::class, $tokens[1]);

        $this->assertSame('somevar', $tokens[2]->getValue());
        $this->assertSame(6, $tokens[2]->getOffset());
        $this->assertInstanceOf(TokenVariable::class, $tokens[2]);
    }

    /** @test */
    public function givenAGrammarWithCollidingRegexWhenPriorityIsWrongItShouldNeverMatchTheOneWithLowerPriority()
    {
        $tokens = $this->tokenizeWithGrammar('somevar   yes', [
            [Token::VARIABLE, '\b[a-z]+\b', 20],
            [Token::BOOL, '\b(?:yes|no)\b', 10],
            [Token::SPACE, '\s+', 5],
        ]);

        $this->assertCount(3, $tokens);

        $this->assertSame('somevar', $tokens[0]->getValue());
        $this->assertSame(0, $tokens[0]->getOffset());
        $this->assertInstanceOf(TokenVariable::class, $tokens[0]);

        $this->assertSame('   ', $tokens[1]->getValue());
        $this->assertSame(7, $tokens[1]->getOffset());
        $this->assertInstanceOf(TokenSpace::class, $tokens[1]);

        $this->assertSame('yes', $tokens[2]->getValue());
        $this->assertSame(10, $tokens[2]->getOffset());
        $this->assertInstanceOf(TokenVariable::class, $tokens[2]);
    }

    /** @test */
    public function givenAGrammarItShouldBeAvailableThroughGetter()
    {
        $grammar = $this->getTokenizer([[Token::BOOL, '\b(?:yes|no)\b', 10]])->getGrammar();

        $this->assertInstanceOf(Grammar::class, $grammar);
        $this->assertInternalType('array', $grammar->getDefinition());
        $this->assertCount(1, $grammar->getDefinition());
    }

    /** @return BaseToken[] */
    private function tokenizeWithGrammar(string $rule, array $definition): array
    {
        $stack = $this->getTokenizer($definition)->tokenize($rule);
        /** @var BaseToken[] $tokens */
        $tokens = [];

        foreach ($stack as $token) {
            $tokens[] = $token;
        }

        return $tokens;
    }

    private function getTokenizer(array $definition): Tokenizer
    {
        $grammar = new class($definition) extends Grammar {
            private $definition = [];

            public function __construct(array $definition)
            {
                $this->definition = $definition;
            }

            public function getDefinition(): array
            {
                return $this->definition;
            }
        };

        return new Tokenizer($grammar, new TokenFactory());
    }
}
