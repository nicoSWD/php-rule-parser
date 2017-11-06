<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Grammar;

use nicoSWD\Rules\TokenStream\Token\BaseToken;

abstract class CallableFunction implements CallableUserFunction
{
    /** @var BaseToken */
    protected $token;

    public function __construct(BaseToken $token = null)
    {
        $this->token = $token;
    }
}
