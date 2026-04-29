<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream\Token;

final class TokenEncapsedString extends TokenString
{
    public function getKind(): TokenKind
    {
        return TokenKind::ENCAPSED_STRING;
    }

    public function getValue(): string
    {
        $value = substr(parent::getValue(), 1, -1);

        return $this->unescape($value);
    }

    /**
     * Unescape common escape sequences in the string content.
     *
     * Handles the same sequences as JavaScript string literals:
     * \n, \r, \t, \\, \", \', \$, \0
     */
    private function unescape(string $value): string
    {
        $result = '';
        $length = strlen($value);

        for ($i = 0; $i < $length; $i++) {
            if ($value[$i] === '\\' && $i + 1 < $length) {
                $next = $value[$i + 1];

                $result .= match ($next) {
                    'n' => "\n",
                    'r' => "\r",
                    't' => "\t",
                    '\\' => '\\',
                    '"' => '"',
                    "'" => "'",
                    '$' => '$',
                    '0' => "\0",
                    default => '\\' . $next,
                };

                $i++; // skip the escaped character
            } else {
                $result .= $value[$i];
            }
        }

        return $result;
    }
}
