<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\tests\functions;

use nicoSWD\Rules\Tokenizer;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenInteger;

class UserDefinedFunctionsTest extends \AbstractTestBase
{
    public function testUserDefinedFunction()
    {
        $this->parser->registerFunction('multiply', function (BaseToken $multiplier) : BaseToken {
            if (!$multiplier instanceof TokenInteger) {
                throw new \Exception;
            }

            return new TokenInteger($multiplier->getValue() * 2);
        });


        $rule = 'multiply(four) === eight';

        $this->assertTrue($this->evaluate($rule, ['four' => 4, 'eight' => 8]));

        $this->parser->registerToken(
            Tokenizer::TOKEN_GREATER,
            '\bis\s+greater\s+than\b'
        );

        $this->parser->registerToken(
            Tokenizer::TOKEN_VARIABLE,
            ':\w+'
        );

        $rule = ':this is greater than :that';

        $this->assertTrue($this->evaluate($rule, [
            ':this' => 8,
            ':that' => 7
        ]));
    }
}
