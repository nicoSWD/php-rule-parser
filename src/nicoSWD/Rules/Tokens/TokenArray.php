<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Constants;

/**
 * Class TokenArray
 * @package nicoSWD\Rules\Tokens
 */
final class TokenArray extends BaseToken
{
    /**
     * @return int
     */
    public function getGroup()
    {
        return Constants::GROUP_VALUE;
    }

    /**
     * @return bool
     */
    public function supportsMethodCalls()
    {
        return \true;
    }

    /**
     * @since 0.3.5
     * @return array
     */
    public function toArray()
    {
        $items = [];

        foreach ($this->value as $value) {
            /** @var self $value */
            $val = $value->getValue();

            if ($val instanceof TokenArray) {
                $items += $val->toArray();
            } else {
                $items[] = $val;
            }
        }

        return $items;
    }
}
