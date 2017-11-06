<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\TokenStream\Token;

use nicoSWD\Rules\TokenStream\Node\NodeString;
use nicoSWD\Rules\TokenStream\TokenStream;

class TokenString extends BaseToken
{
    public function getType(): int
    {
        return TokenType::VALUE;
    }

    public function createNode(TokenStream $tokenStream): BaseToken
    {
        return (new NodeString($tokenStream))->getNode();
    }
}
