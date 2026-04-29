<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar;

abstract class CallableFunction implements CallableInterface
{
    public function __construct(
        protected readonly mixed $token = null,
    ) {
    }

    protected function parseParameter(array $parameters, int $numParam): mixed
    {
        return $parameters[$numParam] ?? null;
    }
}

