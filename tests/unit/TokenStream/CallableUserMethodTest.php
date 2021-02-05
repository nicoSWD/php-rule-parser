<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\TokenStream;

use nicoSWD\Rule\TokenStream\CallableUserMethod;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\Token\TokenObject;
use PHPUnit\Framework\TestCase;
use stdClass;

final class CallableUserMethodTest extends TestCase
{
    /** @test */
    public function givenAnObjectWithAPublicPropertyItShouldBeAccessible(): void
    {
        $object = new stdClass();
        $object->my_test = 123;

        $this->assertSame(123, $this->callMethod($object, 'my_test')->getValue());
    }

    /** @test */
    public function givenAnObjectWithAPublicWhenMethodMatchingItShouldBeUsed(): void
    {
        $object = new class {
            public function my_test()
            {
                return 123;
            }
        };

        $this->assertSame(123, $this->callMethod($object, 'my_test')->getValue());
    }

    /** @test */
    public function givenAnObjectWithAPublicWhenMethodNameWithIsPrefixMatchesItShouldBeUsed(): void
    {
        $object = new class {
            public function is_my_test()
            {
                return 123;
            }

            public function isMyTest()
            {
                return 456;
            }
        };

        $this->assertSame(123, $this->callMethod($object, 'my_test')->getValue());
        $this->assertSame(456, $this->callMethod($object, 'myTest')->getValue());
    }

    /** @test */
    public function givenAnObjectWithAPublicWhenMethodNameWithGetPrefixMatchesItShouldBeUsed(): void
    {
        $object = new class {
            public function get_my_test()
            {
                return 123;
            }

            public function getMyTest()
            {
                return 456;
            }
        };

        $this->assertSame(123, $this->callMethod($object, 'my_test')->getValue());
        $this->assertSame(456, $this->callMethod($object, 'myTest')->getValue());
    }

    private function callMethod($object, string $methodName): BaseToken
    {
        $callable = new CallableUserMethod(new TokenObject($object), new TokenFactory(), $methodName);

        return $callable->call();
    }
}
