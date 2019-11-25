<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class ObjectTest extends AbstractTestBase
{
    public function testObjects()
    {
        $myObj = new class {
            function test() {
                return 'test one two';
            }
        };

        $variables = [
            'my_obj' => $myObj,
            'my_string' => 'some test'
        ];

        $this->assertTrue($this->evaluate('my_obj.test() === "test one two"', $variables));
        $this->assertFalse($this->evaluate('my_obj.test() === "oh no"', $variables));
    }
}
