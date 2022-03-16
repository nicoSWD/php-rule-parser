<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Node\NodeFunction;
use nicoSWD\Rule\TokenStream\TokenIterator;

final class TokenFunction extends BaseToken
{
    public function getType(): TokenType
    {
        return TokenType::FUNCTION;
    }

    public function createNode(TokenIterator $tokenStream): BaseToken
    {
        return (new NodeFunction($tokenStream))->getNode();
    }
}
