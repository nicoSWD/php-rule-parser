<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Node\NodeArray;
use nicoSWD\Rule\TokenStream\TokenStream;

final class TokenOpeningArray extends BaseToken
{
    public function getType(): int
    {
        return TokenType::SQUARE_BRACKET;
    }

    public function createNode(TokenStream $tokenStream): BaseToken
    {
        return (new NodeArray($tokenStream))->getNode();
    }
}
