<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Tokens\BaseToken;

/**
 * Class CallableMethod
 * @package nicoSWD\Rules\Core\Methods
 */
abstract class CallableMethod
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
     * @param mixed[] $parameters
     * @return mixed
     */
    abstract public function call(array $parameters = []);
}
