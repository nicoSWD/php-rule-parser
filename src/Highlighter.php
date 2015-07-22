<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use SplObjectStorage;

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
     * @var TokenizerInterface
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
     * @param int    $group
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

        $this->styles[$group] = (string) $style;
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
     * @param SplObjectStorage $tokens
     * @return string
     */
    public function highlightTokens(SplObjectStorage $tokens)
    {
        $string = '';

        foreach ($tokens as $token) {
            if ($style = $this->styles[$token->getGroup()]) {
                $value = htmlentities($token->getValue(), \ENT_QUOTES, 'utf-8');
                $string .= '<span style="' . $style . '">' . $value . '</span>';
            } else {
                $string .= $token->getValue();
            }
        }

        return '<pre><code>' . $string . '</code></pre>';
    }
}
