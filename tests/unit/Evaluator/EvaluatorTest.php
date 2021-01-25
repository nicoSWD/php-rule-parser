<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\Evaluator;

use nicoSWD\Rule\Evaluator\Evaluator;
use nicoSWD\Rule\Evaluator\Exception\UnknownSymbolException;
use PHPUnit\Framework\TestCase;

final class EvaluatorTest extends TestCase
{
    private Evaluator $evaluator;

    protected function setUp(): void
    {
        $this->evaluator = new Evaluator();
    }

    /** @test */
    public function givenACompiledRuleWithAnLogicalAndItShouldEvaluateBothOperandsAndReturnTheResult(): void
    {
        $this->assertTrue($this->evaluator->evaluate('1&1'));
        $this->assertFalse($this->evaluator->evaluate('1&0'));
        $this->assertFalse($this->evaluator->evaluate('0&1'));
        $this->assertFalse($this->evaluator->evaluate('0&0'));
    }

    /** @test */
    public function givenACompiledRuleWithAnLogicalOrItShouldEvaluateBothOperandsAndReturnTheResult(): void
    {
        $this->assertTrue($this->evaluator->evaluate('1|1'));
        $this->assertTrue($this->evaluator->evaluate('1|0'));
        $this->assertTrue($this->evaluator->evaluate('0|1'));
        $this->assertFalse($this->evaluator->evaluate('0|0'));
    }

    /** @test */
    public function givenACompiledRuleWithGroupsTheyShouldBeEvaluatedFirst(): void
    {
        $this->assertTrue($this->evaluator->evaluate('0|(1|0)'));
        $this->assertTrue($this->evaluator->evaluate('1|(0|0)'));
        $this->assertTrue($this->evaluator->evaluate('0|(0|(0|1))'));
        $this->assertFalse($this->evaluator->evaluate('0|(0|(0|0))'));
        $this->assertFalse($this->evaluator->evaluate('0|(0|(1&0))'));
    }

    /** @test */
    public function givenACharacterWhenUnknownItShouldThrowAnException(): void
    {
        $this->expectException(UnknownSymbolException::class);
        $this->expectExceptionMessage('Unexpected "3"');

        $this->evaluator->evaluate('3|1');
    }
}
