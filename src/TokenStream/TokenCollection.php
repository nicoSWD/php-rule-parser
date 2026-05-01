<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream;

use ArrayObject;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

final class TokenCollection extends ArrayObject
{
    public function add(BaseToken $token): void
    {
        $this->append($token);
    }
}
