<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules;

final class Highlighter
{
    /** @var string[] */
    private $styles = [
        TokenType::COMMENT         => 'color: #948a8a; font-style: italic;',
        TokenType::LOGICAL         => 'color: #c72d2d;',
        TokenType::OPERATOR        => 'color: #000;',
        TokenType::PARENTHESES     => 'color: #000;',
        TokenType::SPACE           => '',
        TokenType::UNKNOWN         => '',
        TokenType::VALUE           => 'color: #e36700; font-style: italic;',
        TokenType::VARIABLE        => 'color: #007694; font-weight: 900;',
        TokenType::METHOD          => 'color: #000',
        TokenType::SQUARE_BRACKETS => '',
        TokenType::COMMA           => ''
    ];

    /** @var TokenizerInterface */
    private $tokenizer;

    public function __construct(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /** @throws Exceptions\HighlighterException */
    public function setStyle(int $group, string $style)
    {
        if (!isset($this->styles[$group])) {
            throw new Exceptions\HighlighterException(
                'Invalid group'
            );
        }

        $this->styles[$group] = (string) $style;
    }

    /** @throws Exceptions\HighlighterException */
    public function highlightString(string $string): string
    {
        return $this->highlightTokens($this->tokenizer->tokenize($string));
    }

    public function highlightTokens(Stack $tokens): string
    {
        $string = '';

        foreach ($tokens as $token) {
            if ($style = $this->styles[$token->getType()]) {
                $value = htmlentities($token->getOriginalValue(), \ENT_QUOTES, 'utf-8');
                $string .= '<span style="' . $style . '">' . $value . '</span>';
            } else {
                $string .= $token->getOriginalValue();
            }
        }

        return '<pre><code>' . $string . '</code></pre>';
    }
}
