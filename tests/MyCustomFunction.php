<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\tests;

use nicoSWD\Rules\Core\CallableUserFunction;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenInteger;

class MyCustomFunction implements CallableUserFunction
{
    /**
     * @param BaseToken $param
     * @param BaseToken $param ...
     * @return BaseToken
     */
    public function call($param = null): BaseToken
    {
        return new TokenInteger($param->getValue() * 2);
    }

    public function getName() : string
    {
        return 'double_value';
    }
}
