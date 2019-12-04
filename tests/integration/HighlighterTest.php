<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule;
use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Highlighter\Highlighter;
use nicoSWD\Rule\Tokenizer\Tokenizer;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use PHPUnit\Framework\TestCase;

final class HighlighterTest extends TestCase
{
    /** @var Highlighter */
    private $highlighter;

    protected function setUp()
    {
        $this->highlighter = new Highlighter(new Tokenizer(new JavaScript(), new TokenFactory()));
    }

    /** @test */
    public function givenAStyleForATokenGroupItShouldBeUsed()
    {
        $this->highlighter->setStyle(
            Rule\TokenStream\Token\TokenType::SQUARE_BRACKET,
            'color: gray;'
        );

        $code = $this->highlighter->highlightString('[1, 2] == "1,2".split(",") && parseInt(foo) === 12');

        $this->assertContains('<span style="color: gray;">[</span>', $code);
    }

    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionMessage Invalid group
     */
    public function invalidGroupThrowsException()
    {
        $this->highlighter->setStyle(
            99,
            'color: test-color;'
        );
    }
}
