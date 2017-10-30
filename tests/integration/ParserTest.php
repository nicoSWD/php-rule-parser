<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\tests;

use nicoSWD\Rules\tests\integration\AbstractTestBase;

class ParserTest extends AbstractTestBase
{
    public function testMultipleAnds()
    {
        $rule = 'foo=="MA" && bar=="EGP" && baz>50000';

        $this->assertTrue($this->evaluate($rule, [
            'foo' => 'MA',
            'bar' => 'EGP',
            'baz' => '50001',
        ]));

        $rule = 'foo == "EG" && bar=="EGP" && baz>50000';

        $this->assertFalse($this->evaluate($rule, [
            'foo' => 'MA',
            'bar' => 'EGP',
            'baz' => '50001',
        ]));

        $rule = '((foo=="EG") && (bar=="EGP") && (baz>50000))';

        $this->assertFalse($this->evaluate($rule, [
            'foo' => 'MA',
            'bar' => 'EGP',
            'baz' => '50001',
        ]));
    }

    public function testMixedOrsAndAnds()
    {
        $rule = '
            bar=="MA" &&
            foo=="EGP" && (
            baz>50000 ||
            baz == 0)';

        $this->assertTrue($this->evaluate($rule, [
            'bar' => 'MA',
            'foo' => 'EGP',
            'baz' => '50001',
        ]));
    }

    public function testEmptyOrIncompleteRuleReturnsFalse()
    {
        $rule = '';
        $this->assertFalse($this->evaluate($rule));
    }

    public function testFreakingLongRule()
    {
        $rule = '
            bar=="SA" && (qux=="0002950182" ||
            qux=="100130" || qux=="100143" ||
            qux=="100149" || qux=="0002951129" ||
            qux=="0002950746" || qux=="0002950747" ||
            qux=="0002950748" || qux=="0002950749" ||
            qux=="100392" || qux=="0002950751" ||
            qux=="0002950897" || qux=="100208" ||
            qux=="0002951140" || qux=="100209") &&
            BAR==1';

        $this->assertTrue($this->evaluate($rule, [
            'bar' => 'SA',
            'qux' => '0002950751',
            'BAR' => 1,
        ]));

        $this->assertFalse($this->evaluate($rule, [
            'bar' => 'SA',
            'qux' => '0002950751',
            'BAR' => '0',
        ]));
    }

    public function testNegativeComparison()
    {
        $rule = '
            bar !== "EG" &&
            qux!="55350000" &&
            qux!="55358500" &&
            qux!="55303100" &&
            foo=="MAD" &&
            baz>500000 &&
            baz<=1000000';

        $this->assertTrue($this->evaluate($rule, [
            'bar' => 'MA',
            'foo' => 'MAD',
            'qux' => '0002950751',
            'baz' => '999999',
        ]));
    }

    public function testSpacesBetweenStuff()
    {
        $rule = 'foo   !=   3
                &&    3        !=   foo
                    && ( (  foo   ==   foo   )
                        &&   -2   <
                foo
            )';

        $this->assertTrue($this->evaluate($rule, [
            'foo' => '-1',
        ]));
    }

    public function testSingleLineCommentDoesNotKillTheRest()
    {
        $rule = ' 2 > 3

                // &&    3        !=   foo

                || foo == -1
            ';

        $this->assertTrue($this->evaluate($rule, [
            'foo' => '-1',
        ]));
    }
}
