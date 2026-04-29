<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\TokenStream;

final readonly class EvaluationContext
{
    public function __construct(
        public TokenStream  $tokenStream,
        public TokenFactory $tokenFactory,
    ) {
    }
}
