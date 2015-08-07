<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\methods;

/**
 * Class ToUpperCaseTest
 */
class ToUpperCaseTest extends \AbstractTestBase
{
    public function testSpacesBetweenVariableAndMethodWork()
    {
        $this->assertTrue($this->evaluate('foo . toUpperCase() === "BAR"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate(
            'foo
                .
            toUpperCase() === "BAR"',
            ['foo' => 'bar']
        ));
    }

    /**
     * @todo : Rethink tokenizer
     */
    public function testCommentsBeforeAndAfterPeriodWord()
    {
      //  $this->assertTrue($this->evaluate('"bar" /* what */ . /*what */ toUpperCase() === "BAR"'));
    }

    public function testIfCallOnStringLiteralsWorks()
    {
        $this->assertTrue($this->evaluate('"bar".toUpperCase() === "BAR"'));
        $this->assertTrue($this->evaluate('"bar" . toUpperCase() === "BAR"'));
    }

    public function testIfMethodCanBeCalledOnVariablesHoldingIntegers()
    {
        $this->assertTrue($this->evaluate('foo.toUpperCase() === "1"', ['foo' => 1]));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown token ".toUpperCase(" at position 1 on line 1
     */
    public function testCallOnIntegersThrowsException()
    {
        $this->assertTrue($this->evaluate('1.toUpperCase() === "1"', ['foo' => 1]));
    }
}
