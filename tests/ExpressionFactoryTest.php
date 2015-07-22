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
