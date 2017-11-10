--TEST--
Tokenizer stack generation with JavaScript grammar
--FILE--
<?php

use nicoSWD\Rule\Tokenizer\Tokenizer;
use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

require_once __DIR__ . '/../../../vendor/autoload.php';

$tokenizer = new Tokenizer(new JavaScript(), new TokenFactory());

$rule = 'parseInt("2") == var_two && ("foo".toUpperCase() === "FOO") || 1 in ["1", 2, var_one]';

var_dump($tokenizer->tokenize($rule));

?>
--EXPECTF--
object(ArrayIterator)#69 (1) {
  ["storage":"ArrayIterator":private]=>
  array(35) {
    [0]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenFunction)#35 (2) {
      ["value":protected]=>
      string(9) "parseInt("
      ["offset":protected]=>
      int(0)
    }
    [1]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#34 (2) {
      ["value":protected]=>
      string(3) ""2""
      ["offset":protected]=>
      int(9)
    }
    [2]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#36 (2) {
      ["value":protected]=>
      string(1) ")"
      ["offset":protected]=>
      int(12)
    }
    [3]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#37 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(13)
    }
    [4]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEqual)#38 (2) {
      ["value":protected]=>
      string(2) "=="
      ["offset":protected]=>
      int(14)
    }
    [5]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#39 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(16)
    }
    [6]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenVariable)#40 (2) {
      ["value":protected]=>
      string(7) "var_two"
      ["offset":protected]=>
      int(17)
    }
    [7]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#41 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(24)
    }
    [8]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenAnd)#42 (2) {
      ["value":protected]=>
      string(2) "&&"
      ["offset":protected]=>
      int(25)
    }
    [9]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#43 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(27)
    }
    [10]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenOpeningParenthesis)#44 (2) {
      ["value":protected]=>
      string(1) "("
      ["offset":protected]=>
      int(28)
    }
    [11]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#45 (2) {
      ["value":protected]=>
      string(5) ""foo""
      ["offset":protected]=>
      int(29)
    }
    [12]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenMethod)#46 (2) {
      ["value":protected]=>
      string(13) ".toUpperCase("
      ["offset":protected]=>
      int(34)
    }
    [13]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#47 (2) {
      ["value":protected]=>
      string(1) ")"
      ["offset":protected]=>
      int(47)
    }
    [14]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#48 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(48)
    }
    [15]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEqualStrict)#49 (2) {
      ["value":protected]=>
      string(3) "==="
      ["offset":protected]=>
      int(49)
    }
    [16]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#50 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(52)
    }
    [17]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#51 (2) {
      ["value":protected]=>
      string(5) ""FOO""
      ["offset":protected]=>
      int(53)
    }
    [18]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#52 (2) {
      ["value":protected]=>
      string(1) ")"
      ["offset":protected]=>
      int(58)
    }
    [19]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#53 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(59)
    }
    [20]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenOr)#54 (2) {
      ["value":protected]=>
      string(2) "||"
      ["offset":protected]=>
      int(60)
    }
    [21]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#55 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(62)
    }
    [22]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenInteger)#56 (2) {
      ["value":protected]=>
      string(1) "1"
      ["offset":protected]=>
      int(63)
    }
    [23]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#57 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(64)
    }
    [24]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenIn)#58 (2) {
      ["value":protected]=>
      string(2) "in"
      ["offset":protected]=>
      int(65)
    }
    [25]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#59 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(67)
    }
    [26]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenOpeningArray)#60 (2) {
      ["value":protected]=>
      string(1) "["
      ["offset":protected]=>
      int(68)
    }
    [27]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#61 (2) {
      ["value":protected]=>
      string(3) ""1""
      ["offset":protected]=>
      int(69)
    }
    [28]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenComma)#62 (2) {
      ["value":protected]=>
      string(1) ","
      ["offset":protected]=>
      int(72)
    }
    [29]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#63 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(73)
    }
    [30]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenInteger)#64 (2) {
      ["value":protected]=>
      string(1) "2"
      ["offset":protected]=>
      int(74)
    }
    [31]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenComma)#65 (2) {
      ["value":protected]=>
      string(1) ","
      ["offset":protected]=>
      int(75)
    }
    [32]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#66 (2) {
      ["value":protected]=>
      string(1) " "
      ["offset":protected]=>
      int(76)
    }
    [33]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenVariable)#67 (2) {
      ["value":protected]=>
      string(7) "var_one"
      ["offset":protected]=>
      int(77)
    }
    [34]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenClosingArray)#68 (2) {
      ["value":protected]=>
      string(1) "]"
      ["offset":protected]=>
      int(84)
    }
  }
}
