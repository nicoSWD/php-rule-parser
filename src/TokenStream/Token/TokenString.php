<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Node\NodeString;
use nicoSWD\Rule\TokenStream\Token\Type\Value;
use nicoSWD\Rule\TokenStream\TokenIterator;

class TokenString extends BaseToken implements Value
{
    public function getType(): TokenType
    {
        return TokenType::VALUE;
    }

    public function createNode(TokenIterator $tokenStream): BaseToken
    {
        return (new NodeString($tokenStream))->getNode();
    }
}
