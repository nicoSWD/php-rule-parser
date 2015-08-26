<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

/**
 * Class Highlighter
 * @package nicoSWD\Rules
 */
final class Highlighter
{
    /**
     * @var string[]
     */
    private $styles = [
        Constants::GROUP_COMMENT         => 'color: #948a8a; font-style: italic;',
        Constants::GROUP_LOGICAL         => 'color: #c72d2d;',
        Constants::GROUP_OPERATOR        => 'color: #000;',
        Constants::GROUP_PARENTHESES     => 'color: #000;',
        Constants::GROUP_SPACE           => '',
        Constants::GROUP_UNKNOWN         => '',
        Constants::GROUP_VALUE           => 'color: #e36700; font-style: italic;',
        Constants::GROUP_VARIABLE        => 'color: #007694; font-weight: 900;',
        Constants::GROUP_METHOD          => 'color: #000',
        Constants::GROUP_SQUARE_BRACKETS => '',
        Constants::GROUP_COMMA           => ''
    ];

    /**
     * @var TokenizerInterface
     */
    private $tokenizer;

    /**
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @param int $group
     * @param string $style
     * @throws Exceptions\HighlighterException
     */
    public function setStyle($group, $style)
    {
        if (!isset($this->styles[$group])) {
            throw new Exceptions\HighlighterException(
                'Invalid group'
            );
        }

        $this->styles[$group] = (string)$style;
    }

    /**
     * @param string $string
     * @return string
     * @throws Exceptions\HighlighterException
     */
    public function highlightString($string)
    {
        return $this->highlightTokens($this->tokenizer->tokenize($string));
    }

    /**
     * @param Stack $tokens
     * @return string
     */
    public function highlightTokens(Stack $tokens)
    {
        $string = '';

        foreach ($tokens as $token) {
            if ($style = $this->styles[$token->getGroup()]) {
                $value = htmlentities($token->getOriginalValue(), \ENT_QUOTES, 'utf-8');
                $string .= '<span style="' . $style . '">' . $value . '</span>';
            } else {
                $string .= $token->getOriginalValue();
            }
        }

        return '<pre><code>' . $string . '</code></pre>';
    }
}
