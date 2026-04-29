<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Parser;

use nicoSWD\Rule\Expression\ExpressionFactory;

final class EvaluableExpressionFactory
{
    public function create(): EvaluableExpression
    {
        return new EvaluableExpression(
            new ExpressionFactory()
        );
    }
}
