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
    public function __construct(protected ?BaseToken $token = null)
    {
    }

    protected function parseParameter(array $parameters, int $numParam): ?BaseToken
    {
        return $parameters[$numParam] ?? null;
    }
}
