<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Core\Functions;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\TokenFloat;

final class ParseFloat extends CallableFunction
{
    /**
     * {@inheritdoc}
     */
    public function call($value = null) : TokenFloat
    {
        if ($value === null) {
            return new TokenFloat(NAN);
        }

        return new TokenFloat(
            (float) $value->getValue(),
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'parseFloat';
    }
}
