<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\unit\TokenStream;

use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenInteger;

final class TestFunc implements CallableInterface
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        return new TokenInteger(234);
    }
}
