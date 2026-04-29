<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Highlighter;

use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Tokenizer\Lexer;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\Token\TokenType;

/**
 * Syntax highlighter for rule expressions.
 *
 * Takes a rule string and returns HTML with syntax-highlighted tokens,
 * where each token type is wrapped in a <span> with configurable CSS styles.
 *
 * Usage:
 * ```php
 * $highlighter = new Highlighter();
 *
 * // Use default styles
 * echo $highlighter->highlightString('2 < 3 && foo in [4, 6, 7]');
 *
 * // Or customize styles for specific token types
 * $highlighter->setStyle(TokenType::VARIABLE, 'color: #007694; font-weight: 900;');
 * echo $highlighter->highlightString($ruleStr);
 * ```
 */
final class Highlighter
{
    /** @var array<string, string> */
    private array $styles = [];

    public function __construct(
        private readonly ?Grammar $grammar = null,
    ) {
    }

    /**
     * Set the CSS style for a specific token type.
     *
     * If not called, default styles from DefaultStyles will be used.
     */
    public function setStyle(TokenType $type, string $css): self
    {
        $this->styles[$type->name] = $css;

        return $this;
    }

    /**
     * Highlight a rule string and return HTML with syntax highlighting.
     *
     * @param string $rule The rule expression to highlight
     * @return string HTML with <span> tags wrapping each token
     */
    public function highlightString(string $rule): string
    {
        $styles = $this->getResolvedStyles();
        $grammar = $this->grammar ?? new JavaScript();
        $tokenFactory = new TokenFactory();
        $lexer = new Lexer($grammar, $tokenFactory);
        $tokens = $lexer->tokenize($rule);
        $output = '';

        foreach ($tokens as $token) {
            $type = $token->getType();
            $value = $token->getOriginalValue();
            $css = $styles[$type->name] ?? '';

            if ($css === '') {
                $output .= htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            } else {
                $output .= sprintf(
                    '<span style="%s">%s</span>',
                    $css,
                    htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                );
            }
        }

        return $output;
    }

    /**
     * @return array<string, string>
     */
    private function getResolvedStyles(): array
    {
        if ($this->styles !== []) {
            return $this->styles;
        }

        return DefaultStyles::getStyles();
    }
}
