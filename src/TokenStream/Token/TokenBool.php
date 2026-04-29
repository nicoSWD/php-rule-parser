<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream\Token;

final class TokenBool extends GenericToken
{
    public function __construct(
        private readonly TokenKind $kind,
        mixed $value,
        int $offset = 0,
    ) {
        parent::__construct($kind, $value, $offset);
    }

    public static function fromBool(bool $bool): self
    {
        return new self(
            $bool ? TokenKind::BOOL_TRUE : TokenKind::BOOL_FALSE,
            $bool,
        );
    }
}
