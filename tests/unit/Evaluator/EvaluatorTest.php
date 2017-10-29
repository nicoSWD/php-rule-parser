<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\unit\Evaluator;

use nicoSWD\Rules\Evaluator\Evaluator;
use PHPUnit\Framework\TestCase;

class EvaluatorTest extends TestCase
{
    /** @var Evaluator */
    private $evaluator;

    protected function setUp()
    {
        $this->evaluator = new Evaluator();
    }

    /** @test */
    public function givenACompiledRuleWithAnLogicalAndItShouldEvaluateBothOperandsAndReturnTheResult()
    {
        $this->assertTrue($this->evaluator->evaluate('1&1'));
        $this->assertFalse($this->evaluator->evaluate('1&0'));
        $this->assertFalse($this->evaluator->evaluate('0&1'));
        $this->assertFalse($this->evaluator->evaluate('0&0'));
    }

    /** @test */
    public function givenACompiledRuleWithAnLogicalOrItShouldEvaluateBothOperandsAndReturnTheResult()
    {
        $this->assertTrue($this->evaluator->evaluate('1|1'));
        $this->assertTrue($this->evaluator->evaluate('1|0'));
        $this->assertTrue($this->evaluator->evaluate('0|1'));
        $this->assertFalse($this->evaluator->evaluate('0|0'));
    }

    /** @test */
    public function givenACompiledRuleWithGroupsTheyShouldBeEvaluatedFirst()
    {
        $this->assertTrue($this->evaluator->evaluate('0|(1|0)'));
        $this->assertTrue($this->evaluator->evaluate('1|(0|0)'));
        $this->assertTrue($this->evaluator->evaluate('0|(0|(0|1))'));
        $this->assertFalse($this->evaluator->evaluate('0|(0|(0|0))'));
        $this->assertFalse($this->evaluator->evaluate('0|(0|(1&0))'));
    }
}
