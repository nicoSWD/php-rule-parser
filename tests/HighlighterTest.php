<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.5
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules;

/**
 * Class HighlighterTest
 */
class HighlighterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Rules\Highlighter
     */
    private $highlighter;

    public function setup()
    {
        $this->highlighter = new Rules\Highlighter(new Rules\Tokenizer());
    }

    public function testGetMatchedTokenReturnsFalseOnFailure()
    {
        $this->highlighter->setStyle(
            Rules\Constants::GROUP_SQUARE_BRACKETS,
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
