<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Expressions\Factory;

/**
 * Class ExpressionFactoryTest
 */
class ExpressionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    public function setup()
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
