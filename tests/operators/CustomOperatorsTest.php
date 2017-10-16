<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.4.0
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\operators;

use nicoSWD\Rules\Rule;
use nicoSWD\Rules\Token;

class CustomOperatorsTest extends \AbstractTestBase
{
    public function testCustomOperators()
    {
        $rule = new Rule(':this is greater than :that', [
            ':this' => 8,
            ':that' => 7
        ]);

        $rule->registerToken(Token::GREATER, '\bis\s+greater\s+than\b');
        $rule->registerToken(Token::TOKEN_VARIABLE, ':\w+');

        $this->assertTrue($rule->isTrue());
    }
}
