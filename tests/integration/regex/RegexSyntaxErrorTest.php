<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration\regex;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class RegexSyntaxErrorTest extends AbstractTestBase
{
    #[Test]
    public function duplicateModifierIThrowsException(): void
    {
        $rule = new Rule('/^foo$/.test("foo") === true');

        $this->assertTrue($rule->isValid());

        $rule = new Rule('/^foo$/ii.test("foo") === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Duplicate regex modifier "i" at position 0', $rule->error);
    }

    #[Test]
    public function duplicateModifierGThrowsException(): void
    {
        $rule = new Rule('/^foo$/gg.test("foo") === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Duplicate regex modifier "g" at position 0', $rule->error);
    }

    #[Test]
    public function duplicateModifierMThrowsException(): void
    {
        $rule = new Rule('/^foo$/mm.test("foo") === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Duplicate regex modifier "m" at position 0', $rule->error);
    }

    #[Test]
    public function duplicateModifierInCombinationThrowsException(): void
    {
        $rule = new Rule('/^foo$/iig.test("foo") === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Duplicate regex modifier "i" at position 0', $rule->error);
    }

    #[Test]
    public function duplicateModifierAtEndThrowsException(): void
    {
        $rule = new Rule('/^foo$/igg.test("foo") === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Duplicate regex modifier "g" at position 0', $rule->error);
    }

    #[Test]
    public function noModifiersIsValid(): void
    {
        $rule = new Rule('/^foo$/.test("foo") === true');

        $this->assertTrue($rule->isValid());
    }

    #[Test]
    public function singleModifierIsValid(): void
    {
        $rule = new Rule('/^foo$/i.test("foo") === true');

        $this->assertTrue($rule->isValid());
    }

    #[Test]
    public function allModifiersWithoutDuplicatesIsValid(): void
    {
        $rule = new Rule('/^foo$/igm.test("foo") === true');

        $this->assertTrue($rule->isValid());
    }

    #[Test]
    public function modifiersInDifferentOrderIsValid(): void
    {
        $rule = new Rule('/^foo$/mgi.test("foo") === true');

        $this->assertTrue($rule->isValid());
    }
}
