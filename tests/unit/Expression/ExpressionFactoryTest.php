<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\unit\Expression;

use nicoSWD\Rule\Expression;
use nicoSWD\Rule\Expression\ExpressionFactory;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ExpressionFactoryTest extends TestCase
{
    private ExpressionFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ExpressionFactory();
    }

    #[Test]
    #[DataProvider('expressionProvider')]
    public function givenAnEqualOperatorItShouldCreateAnEqualExpression(
        string $expressionClass,
        Token\BaseToken $token
    ): void {
        $this->assertInstanceOf(
            $expressionClass,
            $this->factory->createFromOperator($token)
        );
    }

    public static function expressionProvider(): array
    {
        return [
            [Expression\EqualExpression::class, new GenericToken(TokenKind::EQUAL, '==')],
            [Expression\EqualStrictExpression::class, new GenericToken(TokenKind::EQUAL_STRICT, '===')],
            [Expression\NotEqualExpression::class, new GenericToken(TokenKind::NOT_EQUAL, '!=')],
            [Expression\NotEqualStrictExpression::class, new GenericToken(TokenKind::NOT_EQUAL_STRICT, '!==')],
            [Expression\GreaterThanExpression::class, new GenericToken(TokenKind::GREATER, '>')],
            [Expression\LessThanExpression::class, new GenericToken(TokenKind::LESS_THAN, '<')],
            [Expression\LessThanEqualExpression::class, new GenericToken(TokenKind::LESS_THAN_EQUAL, '<=')],
            [Expression\GreaterThanEqualExpression::class, new GenericToken(TokenKind::GREATER_EQUAL, '>=')],
            [Expression\InExpression::class, new GenericToken(TokenKind::IN, 'in')],
            [Expression\NotInExpression::class, new GenericToken(TokenKind::NOT_IN, 'not in')],
        ];
    }
}
