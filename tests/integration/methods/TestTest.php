<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class TestTest extends AbstractTestBase
{
    #[Test]
    public function basicRegularExpression(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/.test("foo") === true'));
        $this->assertTrue($this->evaluate('/^foo$/.test(foo) === true', ['foo' => 'foo']));
    }

    #[Test]
    public function arrayIsConvertedToString(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/.test(["foo"]) === true'));
        $this->assertTrue($this->evaluate('/1/.test([[[1]]]) === true'));
    }

    #[Test]
    public function modifiers(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/i.test("FOO") === true'));
        $this->assertFalse($this->evaluate('/^foo$/.test("FOO") === true'));
        $this->assertTrue($this->evaluate('/^foo$/m.test("' . "\n\n" . 'foo") === true'));
        $this->assertFalse($this->evaluate('/^foo$/.test("' . "\n\n" . 'foo") === true'));
    }

    #[Test]
    public function gModifierIsIgnored(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/gi.test("foo") === true'), 'gi');
        $this->assertTrue($this->evaluate('/^foo$/ig.test("foo") === true'), 'ig');
        $this->assertTrue($this->evaluate('/^foo$/g.test("foo") === true'), '"g" modifier alone');
    }

    #[Test]
    public function booleansAndNullsAsSubject(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/.test(true) === false'));
        $this->assertTrue($this->evaluate('/^foo$/.test(false) === false'));
        $this->assertTrue($this->evaluate('/^foo$/.test(null) === false'));
        $this->assertTrue($this->evaluate('/^true/.test(true) === false'));
    }

    #[Test]
    public function withOmittedParameters(): void
    {
        $this->assertTrue($this->evaluate('/^foo$/.test() === false'));
    }

    #[Test]
    public function testAsDirectLogicalOperandWithAnd(): void
    {
        $this->assertTrue($this->evaluate('/a/.test("a") && /x/.test("x")'));
        $this->assertFalse($this->evaluate('/a/.test("b") && /x/.test("x")'));
        $this->assertFalse($this->evaluate('/a/.test("a") && /x/.test("y")'));
    }

    #[Test]
    public function testAsDirectLogicalOperandWithOr(): void
    {
        $this->assertTrue($this->evaluate('/a/.test("a") || /x/.test("y")'));
        $this->assertFalse($this->evaluate('/a/.test("b") || /x/.test("y")'));
    }

    #[Test]
    public function testAsDirectLogicalOperandWithStrictComparison(): void
    {
        $this->assertTrue($this->evaluate('/a/.test("a") === true && /x/.test("x") === true'));
        $this->assertFalse($this->evaluate('/a/.test("b") === true && /x/.test("x") === true'));
    }
}
