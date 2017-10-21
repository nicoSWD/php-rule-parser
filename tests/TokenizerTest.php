<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules;
use nicoSWD\Rules\Grammar\JavaScript\JavaScript;
use nicoSWD\Rules\Tokens\TokenFactory;

class TokenizerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Rules\Tokenizer */
    private $tokenizer;

    protected function setUp()
    {
        $this->tokenizer = new Rules\Tokenizer(new JavaScript(), new TokenFactory());
    }

    public function testGetMatchedTokenReturnsFalseOnFailure()
    {
        $reflection = new \ReflectionMethod($this->tokenizer, 'getMatchedToken');
        $reflection->setAccessible(true);
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
