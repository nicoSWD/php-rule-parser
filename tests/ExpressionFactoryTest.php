<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Expressions\EqualExpression;
use nicoSWD\Rules\Expressions\Factory;
use nicoSWD\Rules\Expressions\NotEqualExpression;

class ExpressionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var Factory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new Factory();
    }

    public function testCorrectInstancesAreCreated()
    {
        $this->assertInstanceOf(
            EqualExpression::class,
            $this->factory->createFromOperator(new \nicoSWD\Rules\Tokens\TokenEqual('=='))
        );
    }

    public function testOperatorMappingReturnsCorrectInstance()
    {
        $operator = new class ('%') extends \nicoSWD\Rules\Tokens\BaseToken {
            public function getType(): int
            {
                return \nicoSWD\Rules\TokenType::OPERATOR;
            }
        };

        $this->factory->mapOperatorToClass($operator, NotEqualExpression::class);

        $this->assertInstanceOf(
            NotEqualExpression::class,
            $this->factory->createFromOperator($operator)
        );
    }
}
