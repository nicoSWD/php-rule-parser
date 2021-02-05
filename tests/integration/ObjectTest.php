<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Rule;

final class ObjectTest extends AbstractTestBase
{
    /** @test */
    public function givenAnObjectHasMethodsWhenPublicTheyShouldBeAccessible(): void
    {
        $myObj = new class {
            public function test()
            {
                return 'test one two';
            }

            public function test2()
            {
                return new class {
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
    public function givenAnObjectHasPropertiesWhenPublicTheyShouldBeAccessible(): void
    {
        $myObj = new class {
            public string $test = 'my string';
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->assertTrue($this->evaluate('my_obj.test() === "my string"', $variables));
    }

    /** @test */
    public function publicMethodsShouldBeAccessibleMagicallyViaGet(): void
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
    public function publicMethodsShouldBeAccessibleMagicallyViaIs(): void
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
    public function givenAnObjectWhenMagicMethodCallIsAvailableItShouldBeAccessible(): void
    {
        $myObj = new class {
            public function __call(string $name, array $args): array
            {
                return [$name, $args[0], $args[1]];
            }
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->assertTrue($this->evaluate('my_obj.my_method("my_arg", 2) === ["my_method", "my_arg", 2]', $variables));
    }

    /**
     * @test
     * @dataProvider phpMagicMethods
     */
    public function givenAnObjectWhenMagicMethodsAreCalledDirectlyItShouldThrowAnException(string $magicMethod): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage("Forbidden method \"{$magicMethod}\" at position 6");

        $myObj = new class() {
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $this->evaluate("my_obj.{$magicMethod}()", $variables);
    }

    /** @test */
    public function undefinedMethodsShouldThrowAnError(): void
    {
        $myObj = new class() {
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $rule = new Rule('my_obj.nope() === false', $variables);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined method "nope" at position 6', $rule->getError());
    }

    /** @test */
    public function givenAnObjectWithMagicMethodGetWhenPropertyDoesNotExistItShouldNotBeCalled(): void
    {
        $myObj = new class {
            public function __get(string $name)
            {
                return 'I should not be called';
            }
        };

        $variables = [
            'my_obj' => $myObj,
        ];

        $rule = new Rule('my_obj.nope() === "nope"', $variables);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined method "nope" at position 6', $rule->getError());
    }

    public function phpMagicMethods(): array
    {
        return [
            ['__construct'],
            ['__destruct'],
            ['__call'],
            ['__callStatic'],
            ['__get'],
            ['__set'],
            ['__isset'],
            ['__unset'],
            ['__sleep'],
            ['__wakeup'],
            ['__toString'],
            ['__invoke'],
            ['__set_state'],
            ['__clone'],
            ['__debugInfo'],
        ];
    }
}
