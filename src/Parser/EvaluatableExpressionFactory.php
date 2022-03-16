<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Parser;

use nicoSWD\Rule\Expression\ExpressionFactory;

final class EvaluatableExpressionFactory
{
    public function create(): EvaluatableExpression
    {
        return new EvaluatableExpression(
            new ExpressionFactory()
        );
    }
}
