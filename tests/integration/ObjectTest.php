<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule\Rule;
use stdClass;

final class ObjectTest extends AbstractTestBase
{
    /** @test */
    public function givenAnObjectHasMethodsWhenPublicTheyShouldBeAccessible()
    {
        $myObj = new class {
            public function test()
            {
                return 'test one two';
            }

            function test2()
            {
                return new class ()
                {
                    public function cat()
                    {
                        return 'meow';
                    }
                };
            }
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->assertTrue($this->evaluate('my_obj.test() === "test one two"', $variables));
        $this->assertTrue($this->evaluate('my_obj.test2().cat() === "meow"', $variables));
    }

    /** @test */
    public function givenAnObjectHasPropertiesWhenPublicTheyShouldBeAccessible()
    {
        $myObj = new class {
            public $test = 'my string';
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->assertTrue($this->evaluate('my_obj.test() === "my string"', $variables));
    }

    /** @test */
    public function publicMethodsShouldBeAccessibleMagicallyViaGet()
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

    /** @test */
    public function publicMethodsShouldBeAccessibleMagicallyViaIs()
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

    /** @test */
    public function undefinedMethodsShouldThrowAnError()
    {
        $myObj = new stdClass();

        $variables = [
            'my_obj' => $myObj,
        ];

        $rule = new Rule('my_obj.nope() === false', $variables);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined method "nope" at position 6', $rule->getError());
    }
}
