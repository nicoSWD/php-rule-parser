<?php
/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 11/07/15
 * Time: 09:56
 */

namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Constants;

/**
 * Class TokenAnd
 * @package nicoSWD\Rules\Tokens
 */
final class TokenAnd extends BaseToken
{
    /**
     * @return int
     */
    public function getGroup()
    {
        return Constants::GROUP_LOGICAL;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return '&';
    }
}
