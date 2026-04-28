<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule;
use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Highlighter\Highlighter;
use nicoSWD\Rule\Tokenizer\Lexer;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class HighlighterTest extends TestCase
{
    private Highlighter $highlighter;
    private Lexer $tokenizer;

    protected function setUp(): void
    {
        $this->highlighter = new Highlighter();
        $this->tokenizer = new Lexer(new JavaScript(), new TokenFactory());
    }

    #[Test]
    public function givenAStyleForATokenGroupItShouldBeUsed(): void
    {
        $this->highlighter->setStyle(
            Rule\TokenStream\Token\TokenType::SQUARE_BRACKET,
            'color: gray;'
        );

        $tokens = $this->tokenizer->tokenize('[1, 2] == "1,2".split(",") && parseInt(foo) === 12');
        $code = $this->highlighter->highlightString('[1, 2] == "1,2".split(",") && parseInt(foo) === 12', $tokens);

        $this->assertStringContainsString('<span style="color: gray;">[</span>', $code);
    }
}
