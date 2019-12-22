<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

abstract class CallableFunction implements CallableUserFunctionInterface
{
    /** @var ?BaseToken */
    protected $token;

    public function __construct(BaseToken $token = null)
    {
        $this->token = $token;
    }

    protected function parseParameter(array $parameters, int $numParam): ?BaseToken
    {
        return $parameters[$numParam] ?? null;
    }
}
