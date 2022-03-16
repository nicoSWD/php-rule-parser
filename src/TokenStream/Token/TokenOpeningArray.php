<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Node\NodeArray;
use nicoSWD\Rule\TokenStream\TokenIterator;

final class TokenOpeningArray extends BaseToken
{
    public function getType(): TokenType
    {
        return TokenType::SQUARE_BRACKET;
    }

    public function createNode(TokenIterator $tokenStream): BaseToken
    {
        return (new NodeArray($tokenStream))->getNode();
    }
}
