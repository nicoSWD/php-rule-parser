<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules;

class HighlighterTest extends \PHPUnit\Framework\TestCase
{
    /** @var Rules\Highlighter */
    private $highlighter;

    protected function setUp()
    {
        $this->highlighter = new Rules\Highlighter(new Rules\Tokenizer(new Rules\Grammar\JavaScript()));
    }

    public function testGetMatchedTokenReturnsFalseOnFailure()
    {
        $this->highlighter->setStyle(
            Rules\TokenType::SQUARE_BRACKETS,
            'color: bracket-test-color;'
        );

        $code = $this->highlighter->highlightString('[1, 2] == "1,2".split(",") && parseInt(foo) === 12', ['foo' => '12']);

        $this->assertContains('<span style="color: bracket-test-color;">[</span>', $code);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid group
     */
    public function testInvalidGroupThrowsException()
    {
        $this->highlighter->setStyle(
            99,
            'color: test-color;'
        );
    }
}
