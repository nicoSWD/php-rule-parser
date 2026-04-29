<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Expression;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

interface ExpressionFactoryInterface
{
    public function createFromOperator(BaseToken $operator): BaseExpression;
}
