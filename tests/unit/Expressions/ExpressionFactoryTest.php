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
use nicoSWD\Rules\Tokens;
use nicoSWD\Rules\Tokens\BaseToken;
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
    public function givenAnEqualOperatorItShouldCreateAnEqualExpression(string $expressionClass, BaseToken $token)
    {
        $this->assertInstanceOf(
            $expressionClass,
            $this->factory->createFromOperator($token)
        );
    }

    public function expressionProvider(): array
    {
        return [
            [Expressions\EqualExpression::class, new Tokens\TokenEqual('==')],
            [Expressions\EqualStrictExpression::class, new Tokens\TokenEqualStrict('===')],
            [Expressions\NotEqualExpression::class, new Tokens\TokenNotEqual('!=')],
            [Expressions\NotEqualStrictExpression::class, new Tokens\TokenNotEqualStrict('!==')],
            [Expressions\GreaterThanExpression::class, new Tokens\TokenGreater('>')],
            [Expressions\LessThanExpression::class, new Tokens\TokenSmaller('<')],
            [Expressions\LessThanEqualExpression::class, new Tokens\TokenSmallerEqual('<=')],
            [Expressions\GreaterThanEqualExpression::class, new Tokens\TokenGreaterEqual('>=')],
            [Expressions\InExpression::class, new Tokens\TokenIn('in')],
        ];
    }
}
