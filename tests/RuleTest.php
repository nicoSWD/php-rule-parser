<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules;

/**
 * Class TokenizerTest
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
}
