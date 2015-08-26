<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules;

/**
 * Class TokenizerTest
 */
class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Rules\Tokenizer
     */
    private $tokenizer;

    public function setup()
    {
        $this->tokenizer = new Rules\Tokenizer();
    }

    public function testGetMatchedTokenReturnsFalseOnFailure()
    {
        $reflection = new \ReflectionMethod($this->tokenizer, 'getMatchedToken');
        $reflection->setAccessible(\true);
        $result = $reflection->invoke($this->tokenizer, []);

        $this->assertSame('Unknown', $result);
    }

    public function testTokenPositionAndLineAreCorrect()
    {
        $tokens = $this->tokenizer->tokenize('1');
        $tokens->rewind();
        $firstToken = $tokens->current();

        $this->assertEquals(1, $firstToken->getLine());
        $this->assertEquals(0, $firstToken->getPosition());
    }
}
