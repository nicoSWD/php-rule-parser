<?php

declare(strict_types=1);

/*
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Evaluator\Evaluator;

class EvaluatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Evaluator
     */
    private $evaluator;

    protected function setUp()
    {
        $this->evaluator = new Evaluator();
    }

    public function testEvalGroupParsesSimpleAndsAndOrs()
    {
        $reflectionMethod = new \ReflectionMethod($this->evaluator, 'evalGroup');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs(
            $this->evaluator,
            [
                [
                    1 => '0|0|0|1|0&1',
                ],
            ]
        );

        $this->assertSame(1, $result);

        $result = $reflectionMethod->invokeArgs(
            $this->evaluator,
            [
                [
                    1 => '0|0|0|0|0&0',
                ],
            ]
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
