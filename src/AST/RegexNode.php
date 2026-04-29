<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

final class RegexNode extends ValueNode
{
    public readonly string $pattern;
    public readonly BaseToken $originalToken;

    public function __construct(string $pattern, BaseToken $originalToken)
    {
        $this->pattern = $pattern;
        $this->originalToken = $originalToken;
    }

    public function evaluate(EvaluationContext $context): string
    {
        return $this->pattern;
    }
}
