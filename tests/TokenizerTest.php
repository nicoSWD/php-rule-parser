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

    public function testVariableStartingWithAndParsesCorrectly()
    {
        $rule = 'country == "foo" && andvar == "bar"';

        $result = $this->tokenizer->tokenize($rule);
        $andVar = \null;

        // Dirt
        foreach ($result as $token) {
            if ($token->getValue() === 'andvar') {
                $andVar = $token;
                break;
            }
        }

        /** @var Rules\Tokens\BaseToken $andVar */
        $this->assertInstanceOf('\nicoSWD\Rules\Tokens\TokenVariable', $andVar);
        $this->assertSame($andVar->getValue(), 'andvar');
        $this->assertSame($andVar->getLine(), 1);
        $this->assertSame($andVar->getOffset(), 20);
        $this->assertSame($andVar->getGroup(), Rules\Constants::GROUP_VARIABLE);
    }
}
