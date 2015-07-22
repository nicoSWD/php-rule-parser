<?php

/**
 * @author   Nicolas Oelgart <nicolas.oelgart@non.schneider-electric.com>
 * @date     26/06/2015
 */
namespace tests\Rules;

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
        $rule = 'country is "foo" && andvar is "bar"';

        $result = $this->tokenizer->tokenize($rule);

        list ( , , , , , , , , $andVar, ) = $result;

        /** @var Rules\Tokens\BaseToken $andVar */
        $this->assertInstanceOf('\nicoSWD\Rules\Tokens\TokenVariable', $andVar);
        $this->assertSame($andVar->getValue(), 'andvar');
        $this->assertSame($andVar->getLine(), 1);
        $this->assertSame($andVar->getOffset(), 20);
        $this->assertSame($andVar->getGroup(), Rules\Constants::GROUP_VARIABLE);
    }
}
