<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

use nicoSWD\Rule;

class RuleTest extends \PHPUnit\Framework\TestCase
{
    public function testBasicRuleWithCommentsEvaluatesCorrectly()
    {
        $string = '
            /**
             * This is a test rule with comments
             */

             // This is true
             2 < 3 && (
                // this is false, because foo does not equal 4
                foo == 4
                // but bar is greater than 6
                || bar > 6
             )';

        $vars = [
            'foo' => 5,
            'bar' => 7
        ];

        $rule = new Rule\Rule($string, $vars);

        $this->assertTrue($rule->isTrue());
        $this->assertTrue(!$rule->isFalse());
    }

    public function testIsValidReturnsFalseOnInvalidSyntax()
    {
        $ruleStr = '(2 == 2) && (1 < 3 && 3 > 2 (1 == 1))';

        $rule = new Rule\Rule($ruleStr);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "(" at position 28 on line 1', $rule->getError());
    }

    public function testIsValidReturnsTrueOnValidSyntax()
    {
        $ruleStr = '(2 == 2) && (1 < 3 && 3 > 2 || (1 == 1))';

        $rule = new Rule\Rule($ruleStr);

        $this->assertTrue($rule->isValid());
        $this->assertEmpty($rule->getError());
        $this->assertTrue($rule->isTrue());
    }
}
