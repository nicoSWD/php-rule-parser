<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\functions;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

class SyntaxErrorTest extends AbstractTestBase
{
    public function testUndefinedFunctionThrowsException()
    {
        $rule = new Rule('nope() === true');

        $this->assertFalse($rule->isValid());
        $this->assertSame('nope is not defined', $rule->getError());
    }

    public function testIncorrectSpellingThrowsException()
    {
        $rule = new Rule('paRSeInt("2") === 2');

        $this->assertFalse($rule->isValid());
        $this->assertSame('paRSeInt is not defined', $rule->getError());
    }
}
