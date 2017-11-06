<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Expression;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

interface ExpressionFactoryInterface
{
    public function createFromOperator(BaseToken $operator): BaseExpression;
}
