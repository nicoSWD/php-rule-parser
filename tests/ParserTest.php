<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
require_once 'AbstractTestBase.php';

/**
 * Class ParserTest
 */
class ParserTest extends AbstractTestBase
{
    public function testMultipleAnds()
    {
        $rule = 'COUNTRY=="MA" && CURRENCY=="EGP" && TOTALAMOUNT>50000';

        $this->assertTrue($this->evaluate($rule, [
            'COUNTRY'     => 'MA',
            'CURRENCY'    => 'EGP',
            'TOTALAMOUNT' => '50001'
        ]));

        $rule = 'COUNTRY == "EG" && CURRENCY=="EGP" && TOTALAMOUNT>50000';

        $this->assertFalse($this->evaluate($rule, [
            'COUNTRY'     => 'MA',
            'CURRENCY'    => 'EGP',
            'TOTALAMOUNT' => '50001'
        ]));

        $rule = '((COUNTRY=="EG") && (CURRENCY=="EGP") && (TOTALAMOUNT>50000))';

        $this->assertFalse($this->evaluate($rule, [
            'COUNTRY'     => 'MA',
            'CURRENCY'    => 'EGP',
            'TOTALAMOUNT' => '50001'
        ]));
    }

    public function testMixedOrsAndAnds()
    {
        $rule = '
            COUNTRY=="MA" &&
            CURRENCY=="EGP" && (
            TOTALAMOUNT>50000 ||
            TOTALAMOUNT == 0)';

        $this->assertTrue($this->evaluate($rule, [
            'COUNTRY'     => 'MA',
            'CURRENCY'    => 'EGP',
            'TOTALAMOUNT' => '50001'
        ]));
    }

    public function testEmptyOrIncompleteRuleReturnsFalse()
    {
        $rule = '';

        $this->assertFalse($this->evaluate($rule, [
            'COUNTRY' => 'MA'
        ]));
    }

    public function testFreakingLongRule()
    {
        $rule = '
            COUNTRY=="SA" && (FOO=="0002950182" ||
            FOO=="100130" || FOO=="100143" ||
            FOO=="100149" || FOO=="0002951129" ||
            FOO=="0002950746" || FOO=="0002950747" ||
            FOO=="0002950748" || FOO=="0002950749" ||
            FOO=="100392" || FOO=="0002950751" ||
            FOO=="0002950897" || FOO=="100208" ||
            FOO=="0002951140" || FOO=="100209") &&
            BAR==1';

        $this->assertTrue($this->evaluate($rule, [
            'COUNTRY' => 'SA',
            'FOO'     => '0002950751',
            'BAR'     => 1
        ]));

        $this->assertFalse($this->evaluate($rule, [
            'COUNTRY' => 'SA',
            'FOO'     => '0002950751',
            'BAR'     => '0'
        ]));
    }

    public function testNegativeComparison()
    {
        $rule = '
            COUNTRY !== "EG" &&
            FOO!="55350000" &&
            FOO!="55358500" &&
            FOO!="55303100" &&
            CURRENCY=="MAD" &&
            TOTALAMOUNT>500000 &&
            TOTALAMOUNT<=1000000';

        $this->assertTrue($this->evaluate($rule, [
            'COUNTRY'     => 'MA',
            'CURRENCY'    => 'MAD',
            'FOO'         => '0002950751',
            'TOTALAMOUNT' => '999999'
        ]));
    }

    public function testAllAvailableOperators()
    {
        $this->assertTrue($this->evaluate('3 == 3'));
        $this->assertTrue($this->evaluate('4 === 4'));
        $this->assertTrue($this->evaluate('"4" == 4'));
        $this->assertTrue($this->evaluate('2 > 1'));
        $this->assertTrue($this->evaluate('1 < 2'));
        $this->assertTrue($this->evaluate('1 <> 2'));
        $this->assertTrue($this->evaluate('1 != 2'));
        $this->assertTrue($this->evaluate('1 <= 2'));
        $this->assertTrue($this->evaluate('2 <= 2'));
        $this->assertTrue($this->evaluate('3 >= 2'));
        $this->assertTrue($this->evaluate('2 >= 2'));

        $this->assertFalse($this->evaluate('2 !== 2'));
    }

    public function testStrictOperators()
    {
        $this->assertFalse($this->evaluate('"4" === 4'));
        $this->assertTrue($this->evaluate('4 === 4'));

        $this->assertTrue($this->evaluate('4 !== "4"'));
        $this->assertFalse($this->evaluate('4 !== 4'));
    }

    public function testBooleans()
    {
        $this->assertTrue($this->evaluate('"0" == false'));
        $this->assertFalse($this->evaluate('"0" === false'));
        $this->assertTrue($this->evaluate('1 == true'));
        $this->assertFalse($this->evaluate('1 === true'));
        $this->assertTrue($this->evaluate('foo == true', ['foo' => 'test']));
        $this->assertFalse($this->evaluate('foo === true', ['foo' => 'test']));
        $this->assertTrue($this->evaluate('foo === true', ['foo' => \true]));
        $this->assertFalse($this->evaluate('foo === true', ['foo' => \false]));
        $this->assertFalse($this->evaluate('foo !== true', ['foo' => \true]));
    }

    public function testNullValues()
    {
        $this->assertTrue($this->evaluate('foo === null', ['foo' => \null]));
        $this->assertTrue($this->evaluate('foo !== null', ['foo' => 0]));
        $this->assertTrue($this->evaluate('foo !== null', ['foo' => '']));
        $this->assertTrue($this->evaluate('foo !== null', ['foo' => \false]));
        $this->assertTrue($this->evaluate('"" == null', ['foo' => \null]));
        $this->assertFalse($this->evaluate('"" === null', ['foo' => \null]));
    }

    public function testFloats()
    {
        $this->assertFalse($this->evaluate('foo === "1.0000034"', ['foo' => 1.0000034]));
        $this->assertFalse($this->evaluate('foo === 1.0000034', ['foo' => '1.0000034']));
        $this->assertTrue($this->evaluate('foo === 1.0000034', ['foo' => 1.0000034]));
        $this->assertTrue($this->evaluate('1.0000035 > 1.0000034'));
        $this->assertTrue($this->evaluate('2 > 1.0000034'));
    }

    public function testArrays()
    {
        $this->assertFalse($this->evaluate('2 === [123, 12]'));
        //$this->assertTrue($this->evaluate('foo === [123, [12]]', ['foo' => [123, [12]]]));
        $this->assertTrue($this->evaluate('[123, 12] === [123, 12]'));
        $this->assertTrue($this->evaluate('[123, 12] === foo && bar === [23]', [
            'foo' => [123, 12],
            'bar' => [23]
        ]));

        $this->assertTrue($this->evaluate('123 in [123, 12
        // test
        ]'));
        $this->assertTrue($this->evaluate('123 in foo', ['foo' => [123, 12]]));
        $this->assertFalse($this->evaluate('"123" in [123, 12]'));
    }

    public function testCommentsAreIgnoredCorrectly()
    {
        $this->assertFalse($this->evaluate('1 == 2 // || 1 == 1'));
        $this->assertTrue($this->evaluate('1 == 1 // && 2 == 1'));
        $this->assertFalse($this->evaluate('1 == 1 /* || 2 == 1 */ && 2 != 2'));
        $this->assertTrue($this->evaluate('1 == 3 /* || 2 == 1 */ || 2 == 2'));
        $this->assertTrue($this->evaluate(
            '1 /* test */ == 1 /* test */ && /* test */ 2 /* test */ == /* test */ 2'
        ));
    }

    public function testNegativeNumbers()
    {
        $rule = 'TOTALAMOUNT > -1 && TOTALAMOUNT < 1';

        $this->assertTrue($this->evaluate($rule, [
            'TOTALAMOUNT' => '0'
        ]));

        $rule = 'TOTALAMOUNT == -1';

        $this->assertTrue($this->evaluate($rule, [
            'TOTALAMOUNT' => -1
        ]));
    }

    public function testSpacesInValues()
    {
        $rule = 'GREETING == "whaddup yall"';

        $this->assertTrue($this->evaluate($rule, [
            'GREETING' => 'whaddup yall'
        ]));
    }

    public function testIsOperator()
    {
        $rule = 'totalamount == -1';

        $this->assertTrue($this->evaluate($rule, [
            'totalamount' => -1
        ]));

        $rule = 'totalamount == 3';

        $this->assertFalse($this->evaluate($rule, [
            'totalamount' => -1
        ]));

        $rule = 'totalamount != 3 && 3 != totalamount';

        $this->assertTrue($this->evaluate($rule, [
            'totalamount' => -1
        ]));

        $this->assertFalse($this->evaluate($rule, [
            'totalamount' => 3
        ]));

        $rule = 'totalamount != 3 && 3 != totalamount';

        $this->assertTrue($this->evaluate($rule, [
            'totalamount' => -3
        ]));
    }

    public function testSpacesBetweenStuff()
    {
        $rule = 'totalamount   !=   3
                &&    3        !=   totalamount
                    && ( (  totalamount   ==   totalamount   )
                        &&   -2   <
                totalamount
            )';

        $this->assertTrue($this->evaluate($rule, [
            'totalamount' => '-1'
        ]));
    }

    public function testSingleLineCommentDoesNotKillTheRest()
    {
        $rule = ' 2 > 3

                // &&    3        !=   totalamount

                || totalamount == -1
            ';

        $this->assertTrue($this->evaluate($rule, [
            'totalamount' => '-1'
        ]));
    }
/*
    public function testDotSeparatedVariablesParse()
    {
        $rule = 'window.title == "test"';

        $this->assertTrue($this->evaluate($rule, [
            'window.title' => 'test'
        ]));
    }
*/
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "(" at position 19 on line 1
     */
    public function testEmptyParenthesisThrowException()
    {
        $rule = '(totalamount != 3) ()';

        $this->evaluate($rule, [
            'totalamount' => '-1'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected "==" at position 11 on line 1
     */
    public function testDoubleIsOperatorThrowsException()
    {
        $rule = 'country == == "EMD"';

        $this->evaluate($rule, [
            'country' => 'GLF',
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected "==" at position 11 on line 1
     */
    public function testDoubleOperatorThrowsException()
    {
        $rule = 'country == == "EMD"';

        $this->evaluate($rule, [
            'country' => 'GLF',
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Incomplete expression for token "==" at position 0 on line 1
     */
    public function testMissingLeftValueThrowsException()
    {
        $rule = '== "EMD"';

        $this->evaluate($rule, [
            'country' => 'GLF',
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Missing operator at position 17 on line 1
     */
    public function testMissingOperatorThrowsException()
    {
        $rule = 'TOTALAMOUNT == -1 TOTALAMOUNT > 10';

        $this->evaluate($rule, [
            'TOTALAMOUNT' => '-1'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Missing operator at position 14 on line 1
     */
    public function testMissingOperatorThrowsException2()
    {
        $rule = 'foo == 2951356 foo=="2951356"';

        $this->evaluate($rule, [
            'foo' => '12347'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Missing opening parenthesis at position 6
     */
    public function testMissingOpeningParenthesisThrowsException()
    {
        $this->evaluate('1 == 1)', []);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Missing closing parenthesis
     */
    public function testMissingClosingParenthesisThrowsException()
    {
        $this->evaluate('(1 == 1', []);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown token "-" at position 10
     */
    public function testMisplacedMinusThrowsException()
    {
        $this->evaluate('1 == 1 && -foo == 1', ['foo' => 1]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Undefined variable "foo" at position 12 on line 2
     */
    public function testUndefinedVariableThrowsException()
    {
        $rule = ' // new line on purpose
            foo == "MA"';

        $this->evaluate($rule, []);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Incomplete expression
     */
    public function testIncompleteExpressionExceptionIsThrownCorrectly()
    {
        $rule = '1 == 1 && COUNTRY';

        $this->evaluate($rule, [
            'COUNTRY' => 'MA'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Undefined variable "COUNTRY" at position 0
     */
    public function testRulesEvaluatesTrueThrowsExceptionsForUndefinedVars()
    {
        $rule = 'COUNTRY=="MA"';

        $this->evaluate($rule, []);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Incomplete and/or condition
     */
    public function testRulesEvaluatesTrueThrowsExceptionsOnSyntaxErrors()
    {
        $rule = 'COUNTRY == "MA" &&';

        $this->evaluate($rule, [
            'COUNTRY' => 'EG'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected "&&" at position 19 on line 1
     */
    public function testMultipleLogicalTokensThrowException()
    {
        $rule = 'COUNTRY == "MA" && &&';

        $this->evaluate($rule, [
            'COUNTRY' => 'EG'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown token "^" at position 16
     */
    public function testUnknownTokenExceptionIsThrown()
    {
        $rule = 'COUNTRY == "MA" ^';

        $this->evaluate($rule, [
            'COUNTRY' => 'MA'
        ]);
    }
}
