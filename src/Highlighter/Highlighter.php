<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Highlighter;

use ArrayIterator;
use nicoSWD\Rule\Tokenizer\TokenizerInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenType;
use SplObjectStorage;

final class Highlighter
{
    private readonly SplObjectStorage $styles;

    public function __construct(
        private readonly TokenizerInterface $tokenizer,
    ) {
        $this->styles = new SplObjectStorage();
        $this->styles[TokenType::COMMENT] = 'color: #948a8a; font-style: italic;';
        $this->styles[TokenType::LOGICAL] = 'color: #c72d2d;';
        $this->styles[TokenType::OPERATOR] = 'color: #000;';
        $this->styles[TokenType::PARENTHESIS] = 'color: #000;';
        $this->styles[TokenType::VALUE] = 'color: #e36700; font-style: italic;';
        $this->styles[TokenType::VARIABLE] = 'color: #007694; font-weight: 900;';
        $this->styles[TokenType::METHOD] = 'color: #000';
    }

    public function setStyle(TokenType $group, string $style): void
    {
        $this->styles[$group] = $style;
    }

    public function highlightString(string $string): string
    {
        return $this->highlightTokens($this->tokenizer->tokenize($string));
    }

    public function highlightTokens(ArrayIterator $tokens): string
    {
        $string = '';

        foreach ($tokens as $token) {
            /** @var BaseToken $token */
            $tokenType = $token->getType();

            if (isset($this->styles[$tokenType])) {
                $string .= '<span style="' . $this->styles[$tokenType] . '">' . $this->encode($token) . '</span>';
            } else {
                $string .= $token->getOriginalValue();
            }
        }

        return '<pre><code>' . $string . '</code></pre>';
    }

    private function encode(BaseToken $token): string
    {
        return htmlentities($token->getOriginalValue(), ENT_QUOTES, 'utf-8');
    }
}
