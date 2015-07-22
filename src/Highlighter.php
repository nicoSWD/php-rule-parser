<?php

/**
 * @author   Nicolas Oelgart <nicolas.oelgart@non.schneider-electric.com>
 * @version  0.1
 */
namespace nicoSWD\Rules;

/**
 * Class Highlighter
 * @package nicoSWD\Rules
 */
final class Highlighter
{
    /**
     * @var array
     */
    private $styles = [
        Constants::GROUP_COMMENT     => 'color: #948a8a; font-style: italic;',
        Constants::GROUP_LOGICAL     => 'color: #c72d2d;',
        Constants::GROUP_OPERATOR    => 'color: #000;',
        Constants::GROUP_PARENTHESES => 'color: #728c9f;',
        Constants::GROUP_SPACE       => '',
        Constants::GROUP_UNKNOWN     => '',
        Constants::GROUP_VALUE       => 'color: #e36700; font-style: italic;',
        Constants::GROUP_VARIABLE    => 'color: #007694; font-weight: 900;'
    ];

    /**
     * @var Tokenizer
     */
    private $tokenizer = \null;

    /**
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @param string $string
     * @return string
     * @throws Exceptions\HighlighterException
     */
    public function highlightString($string)
    {
        try {
            $tokens = $this->tokenizer->tokenize($string);
        } catch (Exceptions\TokenizerException $e) {
            throw new Exceptions\HighlighterException(
                'Unable to highlight string',
                0,
                $e
            );
        }

        return $this->highlightTokens($tokens);
    }

    /**
     * @param Tokens\BaseToken[] $tokens
     * @return string
     */
    public function highlightTokens($tokens)
    {
        $string = '';

        foreach ($tokens as $token) {
            if ($style = $this->styles[$token->getGroup()]) {
                $string .= '<span style="' . $style . '">' . $token->getValue() . '</span>';
            } else {
                $string .= $token->getValue();
            }
        }

        return '<pre><code>' . $string . '</code></pre>';
    }
}
