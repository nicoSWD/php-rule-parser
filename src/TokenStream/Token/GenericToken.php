<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream\Token;

/**
 * A generic token class that replaces many single-purpose token classes.
 * The specific token kind is identified via the TokenKind enum.
 */
final class GenericToken extends BaseToken
{
    public function __construct(
        private readonly TokenKind $kind,
        mixed $value,
        int $offset = 0,
    ) {
        parent::__construct($value, $offset);
    }

    public function getKind(): TokenKind
    {
        return $this->kind;
    }
}
