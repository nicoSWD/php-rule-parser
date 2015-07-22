<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules;

/**
 * Class RuleTest
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicRuleWithCommentsParsesEvaluatesCorrectly()
    {
        $string = '
            /**
             * This is a test rule with comments
             */

             // This is true
             2 < 3 and (
                # this is false, because foo does not equal 4
                foo is 4
                # but bar is greater than 6
                or bar > 6
             )';

        $vars = [
            'foo' => 5,
            'bar' => 7
        ];


        $rule = new Rules\Rule($string, $vars);

        $this->assertTrue($rule->isTrue());
        $this->assertTrue(!$rule->isFalse());
    }

    public function testIsValidReturnsFalseOnInvalidSyntax()
    {
        $ruleStr = '(2 is 2) and (1 < 3 and 3 > 2 (1 is 1))';

        $rule = new Rules\Rule($ruleStr);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected token "(" at position 30 on line 1', $rule->getError());
    }

    public function testIsValidReturnsTrueOnValidSyntax()
    {
        $ruleStr = '(2 is 2) and (1 < 3 and 3 > 2 or (1 is 1))';

        $rule = new Rules\Rule($ruleStr);

        $this->assertTrue($rule->isValid());
        $this->assertEmpty($rule->getError());
        $this->assertTrue($rule->isTrue());
    }
}
