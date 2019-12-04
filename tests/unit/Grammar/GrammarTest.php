<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\Parser;

use nicoSWD\Rule\Grammar\Grammar;
use PHPUnit\Framework\TestCase;

final class GrammarTest extends TestCase
{
    public function testDefaultValues()
    {
        $grammar = new class extends Grammar {
            public function getDefinition(): array
            {
                return [];
            }
        };

        $this->assertSame([], $grammar->getDefinition());
        $this->assertSame([], $grammar->getInternalFunctions());
        $this->assertSame([], $grammar->getInternalMethods());
    }
}
