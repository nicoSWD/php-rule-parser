<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

interface CallableInterface
{
    /** @throws ParserException */
    public function call(mixed ...$param): BaseToken;

}
