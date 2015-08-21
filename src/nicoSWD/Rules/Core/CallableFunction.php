<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core;

use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\AST\TokenCollection;

/**
 * Class CallableFunction
 * @package nicoSWD\Rules\Core\Methods
 */
abstract class CallableFunction
{
    /**
     * @var BaseToken
     */
    protected $token;

    /**
     * @param BaseToken $token
     */
    public function __construct(BaseToken $token)
    {
        $this->token = $token;
    }

    /**
     * @param TokenCollection $parameters
     * @return mixed
     */
    abstract public function call(TokenCollection $parameters);

    /**
     * @return string
     */
    abstract public function getName();
}
