<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Node\NodeVariable;
use nicoSWD\Rule\TokenStream\Token\Type\Value;
use nicoSWD\Rule\TokenStream\TokenIterator;

final class TokenVariable extends BaseToken implements Value
{
    public function getType(): TokenType
    {
        return TokenType::VARIABLE;
    }

    public function createNode(TokenIterator $tokenStream): BaseToken
    {
        return (new NodeVariable($tokenStream))->getNode();
    }
}
