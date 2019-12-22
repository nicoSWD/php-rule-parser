<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\functions;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class SyntaxErrorTest extends AbstractTestBase
{
    /** @test */
    public function undefinedFunctionThrowsException(): void
    {
        $rule = new Rule('nope() === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('nope is not defined at position 0', $rule->getError());
    }

    /** @test */
    public function incorrectSpellingThrowsException(): void
    {
        $rule = new Rule('/* fail */ paRSeInt("2") === 2');

        $this->assertFalse($rule->isValid());
        $this->assertSame('paRSeInt is not defined at position 11', $rule->getError());
    }
}
