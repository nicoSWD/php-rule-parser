<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration\functions;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class SyntaxErrorTest extends AbstractTestBase
{
    #[Test]
    public function undefinedFunctionThrowsException(): void
    {
        $rule = new Rule('nope() === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('nope is not defined at position 0', $rule->error);
    }

    #[Test]
    public function incorrectSpellingThrowsException(): void
    {
        $rule = new Rule('/* fail */ paRSeInt("2") === 2');

        $this->assertFalse($rule->isValid());
        $this->assertSame('paRSeInt is not defined at position 11', $rule->error);
    }
}
