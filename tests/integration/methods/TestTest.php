<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class TestTest extends AbstractTestBase
{
    /** @test */
    public function basicRegularExpression(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/.test("foo") === true'));
        $this->assertTrue($this->evaluate('/^foo$/.test(foo) === true', ['foo' => 'foo']));
    }

    /** @test */
    public function arrayIsConvertedToString(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/.test(["foo"]) === true'));
        $this->assertTrue($this->evaluate('/1/.test([[[1]]]) === true'));
    }

    /** @test */
    public function modifiers(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/i.test("FOO") === true'));
        $this->assertFalse($this->evaluate('/^foo$/.test("FOO") === true'));
        $this->assertTrue($this->evaluate('/^foo$/m.test("' . "\n\n" .'foo") === true'));
        $this->assertFalse($this->evaluate('/^foo$/.test("' . "\n\n" .'foo") === true'));
    }

    /** @test */
    public function gModifierIsIgnored(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/gi.test("foo") === true'), 'gi');
        $this->assertTrue($this->evaluate('/^foo$/ig.test("foo") === true'), 'ig');
        $this->assertTrue($this->evaluate('/^foo$/g.test("foo") === true'), '"g" modifier alone');
    }

    /** @test */
    public function booleansAndNullsAsSubject(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/.test(true) === false'));
        $this->assertTrue($this->evaluate('/^foo$/.test(false) === false'));
        $this->assertTrue($this->evaluate('/^foo$/.test(null) === false'));
        $this->assertTrue($this->evaluate('/^true/.test(true) === false'));
    }

    /** @test */
    public function withOmittedParameters(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/.test() === false'));
    }
}
