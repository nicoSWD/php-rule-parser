<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\TokenStream;

use nicoSWD\Rule\Grammar\CallableUserFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenInteger;

class TestFunc implements CallableUserFunction
{
    public function call(BaseToken $param = null): BaseToken
    {
        return new TokenInteger(234);
    }
}
