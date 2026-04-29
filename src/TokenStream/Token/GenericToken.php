<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream\Token;

/**
 * A generic token class that replaces many single-purpose token classes.
 * The specific token kind is identified via the TokenKind enum.
 */
class GenericToken extends BaseToken
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

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        $items = [];

        foreach ($this->getValue() as $value) {
            if ($value instanceof BaseToken) {
                $items[] = $value->getValue();
            } else {
                $items[] = $value;
            }
        }

        return $items;
    }
}
