<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.5
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\functions;

/**
 * Class SyntaxErrorTest
 */
class SyntaxErrorTest extends \AbstractTestBase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage nope is not defined
     */
    public function testUndefinedFunctionThrowsException()
    {
        $this->evaluate('nope() === true');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage paRSeInt is not defined
     */
    public function testIncorrectSpellingThrowsException()
    {
        $this->evaluate('paRSeInt("2") === 2');
    }
}
