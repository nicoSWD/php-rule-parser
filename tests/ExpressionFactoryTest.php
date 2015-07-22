<?php

/**
 * @author   Nicolas Oelgart <nicolas.oelgart@non.schneider-electric.com>
 * @date     12/11/2014
 */
namespace tests\Offers;

use nicoSWD\Rules\Expressions\Factory;

/**
 * Class RuleEvaluatesTrueTest
 */
class ExpressionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCorrectInstancesAreCreated()
    {
        $this->assertInstanceOf('\nicoSWD\Rules\Expressions\EqualExpression', Factory::createFromOperator('='));
    }

    /**
     * @expectedException \nicoSWD\Rules\Exceptions\ExpressionFactoryException
     * @expectedExceptionMessage Unknown operator "."
     */
    public function testExceptionOnInvalidOperatorIsThrown()
    {
        Factory::createFromOperator('.');
    }
}
