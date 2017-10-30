<?php

declare(strict_types=1);

/*
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Expressions\EqualExpression;
use nicoSWD\Rules\Expressions\ExpressionFactory;

class ExpressionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ExpressionFactory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new ExpressionFactory();
    }

    public function testCorrectInstancesAreCreated()
    {
        $this->assertInstanceOf(
            EqualExpression::class,
            $this->factory->createFromOperator(new \nicoSWD\Rules\Tokens\TokenEqual('=='))
        );
    }
}
