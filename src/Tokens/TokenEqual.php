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
 * Class TokenEqual
 * @package nicoSWD\Rules\Tokens
 */
final class TokenEqual extends BaseToken
{
    /**
     * @return int
     */
    public function getGroup()
    {
        return Constants::GROUP_OPERATOR;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return '=';
    }
}
