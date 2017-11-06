<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Grammar\JavaScript\JavaScript;
use nicoSWD\Rules\Tokenizer\Tokenizer;
use nicoSWD\Rules\TokenStream\Token\TokenFactory;
use PHPUnit\Framework\TestCase;

class TokenizerTest extends TestCase
{
    /** @var Tokenizer */
    private $tokenizer;

    protected function setUp()
    {
        $this->tokenizer = new Tokenizer(new JavaScript(), new TokenFactory());
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

        $this->assertEquals(1, $tokens->current()->getLine());
        $this->assertEquals(0, $tokens->current()->getPosition());
    }
}
