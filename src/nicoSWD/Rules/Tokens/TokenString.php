<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Constants;
use nicoSWD\Rules\Exceptions\ParserException;

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

    /**
     * @return TokenString
     */
    public function toUpperCase()
    {
        return new TokenString(strtoupper($this->value), $this->getOffset(), $this->getStack());
    }

    /**
     * @param string|int $delimiter
     * @return TokenArray
     * @throws ParserException
     */
    public function split($delimiter)
    {
        if (($numArgs = func_num_args()) !== 1) {
            throw new ParserException(sprintf(
                'Method %s expected 1 argument, got %d',
                __METHOD__,
                $numArgs
            ));
        }

        return new TokenArray(explode($delimiter, $this->getValue()), $this->getOffset(), $this->getStack());
    }

    /**
     * @param int $position
     * @return TokenString
     */
    public function charAt($position)
    {
        return new TokenString('"' . $this->getValue()[$position] . '"', $this->getOffset(), $this->getStack());
    }
}
