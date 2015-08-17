<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.5
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\methods;

/**
 * Class TestTest
 */
class TestTest extends \AbstractTestBase
{
    public function testBasicRegularExpression()
    {
        $this->assertTrue($this->evaluate('/^foo$/.test("foo") === true'));
        $this->assertTrue($this->evaluate('/^foo$/.test(foo) === true', ['foo' => 'foo']));
    }

    public function testArrayIsConvertedToString()
    {
        $this->assertTrue($this->evaluate('/^foo$/.test(["foo"]) === true'));
        $this->assertTrue($this->evaluate('/1/.test([[[1]]]) === true'));
    }

    public function testModifiers()
    {
        $this->assertTrue($this->evaluate('/^foo$/i.test("FOO") === true'));
        $this->assertFalse($this->evaluate('/^foo$/.test("FOO") === true'));
        $this->assertTrue($this->evaluate('/^foo$/m.test("' . "\n\n" .'foo") === true'));
        $this->assertFalse($this->evaluate('/^foo$/.test("' . "\n\n" .'foo") === true'));
    }

    public function testGModifierIsIgnored()
    {
        $this->assertTrue($this->evaluate('/^foo$/gi.test("foo") === true'), 'gi');
        $this->assertTrue($this->evaluate('/^foo$/ig.test("foo") === true'), 'ig');
        $this->assertTrue($this->evaluate('/^foo$/g.test("foo") === true'), '"g" modifier alone');
    }

    public function testBooleansAndNullsAsSubject()
    {
        $this->assertTrue($this->evaluate('/^foo$/.test(true) === false'));
        $this->assertTrue($this->evaluate('/^foo$/.test(false) === false'));
        $this->assertTrue($this->evaluate('/^foo$/.test(null) === false'));
        $this->assertTrue($this->evaluate('/^true/.test(true) === false'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage undefined is not a function at position 0 on line 1
     */
    public function testExceptionIsThrownOnTypeError()
    {
        $this->evaluate('"foo".test("foo") === false');
    }
}
