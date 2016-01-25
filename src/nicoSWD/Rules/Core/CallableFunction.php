<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Core;

use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\AST\TokenCollection;

abstract class CallableFunction
{
    /**
     * @var BaseToken
     */
    protected $token;

    public function __construct(BaseToken $token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    abstract public function call(TokenCollection $parameters);

    abstract public function getName() : string;
}
