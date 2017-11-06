<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\unit\Expressions;

use nicoSWD\Rules\Expressions;
use nicoSWD\Rules\Expressions\ExpressionFactory;
use nicoSWD\Rules\TokenStream\Token;
use PHPUnit\Framework\TestCase;

class ExpressionFactoryTest extends TestCase
{
    /** @var ExpressionFactory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new ExpressionFactory();
    }

    /**
     * @test
     * @dataProvider expressionProvider
     */
    public function givenAnEqualOperatorItShouldCreateAnEqualExpression(string $expressionClass, Token\BaseToken $token)
    {
        $this->assertInstanceOf(
            $expressionClass,
            $this->factory->createFromOperator($token)
        );
    }

    public function expressionProvider(): array
    {
        return [
            [Expressions\EqualExpression::class, new Token\TokenEqual('==')],
            [Expressions\EqualStrictExpression::class, new Token\TokenEqualStrict('===')],
            [Expressions\NotEqualExpression::class, new Token\TokenNotEqual('!=')],
            [Expressions\NotEqualStrictExpression::class, new Token\TokenNotEqualStrict('!==')],
            [Expressions\GreaterThanExpression::class, new Token\TokenGreater('>')],
            [Expressions\LessThanExpression::class, new Token\TokenSmaller('<')],
            [Expressions\LessThanEqualExpression::class, new Token\TokenSmallerEqual('<=')],
            [Expressions\GreaterThanEqualExpression::class, new Token\TokenGreaterEqual('>=')],
            [Expressions\InExpression::class, new Token\TokenIn('in')],
        ];
    }
}
