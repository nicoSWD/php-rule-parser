<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Expression;

use nicoSWD\Rules\TokenStream\Token\BaseToken;

interface ExpressionFactoryInterface
{
    public function createFromOperator(BaseToken $operator): BaseExpression;
}
