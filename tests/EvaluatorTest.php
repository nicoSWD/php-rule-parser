<?php

/**
 * @author   Nicolas Oelgart <nicolas.oelgart@non.schneider-electric.com>
 * @date     26/06/2015
 */
namespace tests\Rules;

use nicoSWD\Rules\Evaluator;

/**
 * Class EvaluatorTest
 */
class EvaluatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Evaluator
     */
    private $evaluator;

    public function setup()
    {
        $this->evaluator = new Evaluator();
    }

    public function testEvalGroupParsesSimpleAndsAndOrs()
    {
        $reflectionMethod = new \ReflectionMethod($this->evaluator, 'evalGroup');
        $reflectionMethod->setAccessible(\true);

        $result = $reflectionMethod->invokeArgs(
            $this->evaluator,
            array(
                array(
                    1 => '0|0|0|1|0&1'
                )
            )
        );

        $this->assertSame(1, $result);

        $result = $reflectionMethod->invokeArgs(
            $this->evaluator,
            array(
                array(
                    1 => '0|0|0|0|0&0'
                )
            )
        );

        $this->assertSame(0, $result);
    }

    public function testEvaluateParsesMultipleGroups()
    {
        $result = $this->evaluator->evaluate('((1&(0|0|1|0|0)&1)&1|(0|1))');
        $this->assertTrue($result);

        $result = $this->evaluator->evaluate('((0&(0|0|1|0|0)&1)&1|0)');
        $this->assertFalse($result);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected "3"
     */
    public function testUnknownCharacterThrowsException()
    {
        $this->evaluator->evaluate('((1&(0|3|1|0|0)&1)&1|(0|1))');
    }
}
