<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Tokens\BaseToken;

/**
 * Interface CallableMethod
 * @package nicoSWD\Rules\Core\Methods
 */
interface CallableMethod
{
    /**
     * @param BaseToken $token
     * @param array     $parameters
     * @return mixed
     */
    public function call(BaseToken $token, array $parameters = []);
}
