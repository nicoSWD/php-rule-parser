<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class ObjectTest extends AbstractTestBase
{
    public function testObjects()
    {
        $myObj = new class {
            function test() {
                return 'test one two';
            }

            function test2() {
                return new class ()
                {
                    function miau() {
                        return 'miau';
                    }
                };
            }
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->assertTrue($this->evaluate('my_obj.test() === "test one two"', $variables));
        $this->assertTrue($this->evaluate('my_obj.test2().miau() === "miau"', $variables));
    }

    public function testPublicPropertyShouldBeAccessible()
    {
        $myObj = new class {
            public $test = 'my string';
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->assertTrue($this->evaluate('my_obj.test() === "my string"', $variables));
    }

    public function testPublicMethodsShouldBeAccessibleMagicallyViaGet()
    {
        $myObj = new class {
            public function getString()
            {
                return 'some string';
            }
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->assertTrue($this->evaluate('my_obj.string() === "some string"', $variables));
    }

    public function testPublicMethodsShouldBeAccessibleMagicallyViaIs()
    {
        $myObj = new class {
            public function isString($string)
            {
                return $string;
            }

            public function yes()
            {
                return 'yes';
            }
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->assertTrue($this->evaluate('my_obj.string(my_obj.yes()) === "yes"', $variables));
    }

    public function testUndefinedMethodsShouldThrowAnError()
    {
        $myObj = new class {};

        $variables = [
            'my_obj' => $myObj,
        ];

        $rule = new Rule('my_obj.nope() === false', $variables);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined method "nope" at position 0', $rule->getError());
    }
}
