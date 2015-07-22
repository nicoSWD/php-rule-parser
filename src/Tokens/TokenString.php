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
 * Class TokenString
 * @package nicoSWD\Rules\Tokens
 */
final class TokenString extends BaseToken
{
    /**
     * @return int
     */
    public function getGroup()
    {
        return Constants::GROUP_VALUE;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return substr(parent::getValue(), 1, -1);
    }
}
