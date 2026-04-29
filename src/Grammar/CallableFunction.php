<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

abstract class CallableFunction implements CallableInterface
{
    public function __construct(
        protected readonly ?BaseToken $token = null,
    ) {
    }

    protected function parseParameter(array $parameters, int $numParam): ?BaseToken
    {
        return $parameters[$numParam] ?? null;
    }
}
