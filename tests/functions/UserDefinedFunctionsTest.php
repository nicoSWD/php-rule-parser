<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\tests\functions;

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

        $rule = 'multiply(4) === 8';

        $this->assertTrue($this->evaluate($rule));
    }
}
