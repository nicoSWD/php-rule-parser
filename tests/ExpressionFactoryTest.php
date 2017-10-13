<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Expressions\Factory;

class ExpressionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new Factory();
    }

    public function testCorrectInstancesAreCreated()
    {
        $this->assertInstanceOf(
            '\nicoSWD\Rules\Expressions\EqualExpression',
            $this->factory->createFromOperator('==')
        );
    }

    public function testOperatorMappingReturnsCorrectInstance()
    {
        $this->factory->mapOperatorToClass('+', '\nicoSWD\Rules\Expressions\NotEqualExpression');

        $this->assertInstanceOf(
            '\nicoSWD\Rules\Expressions\NotEqualExpression',
            $this->factory->createFromOperator('+')
        );
    }

    /**
     * @expectedException \nicoSWD\Rules\Exceptions\ExpressionFactoryException
     * @expectedExceptionMessage Unknown operator "."
     */
    public function testExceptionOnInvalidOperatorIsThrown()
    {
        $this->factory->createFromOperator('.');
    }
}
