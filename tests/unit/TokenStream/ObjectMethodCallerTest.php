<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\unit\TokenStream;

use nicoSWD\Rule\TokenStream\ObjectMethodCaller;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectMethodCallerTest extends TestCase
{
    #[Test]
    public function givenAnObjectWithAPublicPropertyItShouldBeAccessible(): void
    {
        $object = new stdClass();
        $object->my_test = 123;

        $this->assertSame(123, $this->callMethod($object, 'my_test')->getValue());
    }

    #[Test]
    public function givenAnObjectWithAPublicWhenMethodMatchingItShouldBeUsed(): void
    {
        $object = new class () {
            // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
            public function my_test()
            {
                return 123;
            }
        };

        $this->assertSame(123, $this->callMethod($object, 'my_test')->getValue());
    }

    #[Test]
    public function givenAnObjectWithAPublicWhenMethodNameWithIsPrefixMatchesItShouldBeUsed(): void
    {
        $object = new class () {
            // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
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

    #[Test]
    public function givenAnObjectWithAPublicWhenMethodNameWithGetPrefixMatchesItShouldBeUsed(): void
    {
        $object = new class () {
            // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
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
        $callable = new ObjectMethodCaller(new GenericToken(TokenKind::OBJECT, $object), new TokenFactory(), $methodName);

        return $callable->call();
    }
}
