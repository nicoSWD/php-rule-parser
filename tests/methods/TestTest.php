<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\methods;

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

    public function testWithOmittedParameters()
    {
        $this->assertTrue($this->evaluate('/^foo$/.test() === false'));
    }
}
