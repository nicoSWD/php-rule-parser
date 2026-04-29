<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Highlighter;

use nicoSWD\Rule\TokenStream\Token\TokenType;

/**
 * Provides sensible default CSS styles for syntax highlighting.
 *
 * These styles are inspired by VS Code's Dark+ theme and provide
 * a good out-of-the-box experience for highlighting rule expressions.
 */
final class DefaultStyles
{
    /** @return array<string, string> */
    public static function getStyles(): array
    {
        return [
            TokenType::OPERATOR->name => 'color: #D4D4D4;',
            TokenType::VALUE->name => 'color: #CE9178;',
            TokenType::LOGICAL->name => 'color: #569CD6;',
            TokenType::VARIABLE->name => 'color: #9CDCFE;',
            TokenType::COMMENT->name => 'color: #6A9955; font-style: italic;',
            TokenType::SPACE->name => '',
            TokenType::UNKNOWN->name => 'color: #FF0000;',
            TokenType::PARENTHESIS->name => 'color: #DCDCA;',
            TokenType::SQUARE_BRACKET->name => 'color: #DCDCA;',
            TokenType::COMMA->name => 'color: #D4D4D4;',
            TokenType::METHOD->name => 'color: #DCDCAA;',
            TokenType::FUNCTION->name => 'color: #DCDCAA;',
        ];
    }
}
