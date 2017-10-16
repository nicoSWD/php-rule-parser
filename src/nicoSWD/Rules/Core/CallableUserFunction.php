<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core;

use nicoSWD\Rules\Tokens\BaseToken;

interface CallableUserFunction
{
    /**
     * @param BaseToken $param
     * @param BaseToken $param ...
     * @return BaseToken
     */
    public function call($param = null): BaseToken;

    public function getName() : string;
}
