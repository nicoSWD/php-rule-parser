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

--EXPECTF--
object(ArrayIterator)#%d (1) {
  ["storage":"ArrayIterator":private]=>
  array(35) {
    [0]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenFunction)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(9) "parseInt("
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(0)
    }
    [1]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(3) ""2""
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(9)
    }
    [2]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) ")"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(12)
    }
    [3]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(13)
    }
    [4]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEqual)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(2) "=="
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(14)
    }
    [5]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(16)
    }
    [6]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenVariable)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(7) "var_two"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(17)
    }
    [7]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(24)
    }
    [8]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenAnd)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(2) "&&"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(25)
    }
    [9]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(27)
    }
    [10]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenOpeningParenthesis)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) "("
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(28)
    }
    [11]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(5) ""foo""
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(29)
    }
    [12]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenMethod)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(13) ".toUpperCase("
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(34)
    }
    [13]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) ")"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(47)
    }
    [14]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(48)
    }
    [15]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEqualStrict)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(3) "==="
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(49)
    }
    [16]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(52)
    }
    [17]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(5) ""FOO""
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(53)
    }
    [18]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) ")"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(58)
    }
    [19]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(59)
    }
    [20]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenOr)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(2) "||"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(60)
    }
    [21]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(62)
    }
    [22]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenInteger)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) "1"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(63)
    }
    [23]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(64)
    }
    [24]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenIn)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(2) "in"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(65)
    }
    [25]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(67)
    }
    [26]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenOpeningArray)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) "["
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(68)
    }
    [27]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(3) ""1""
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(69)
    }
    [28]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenComma)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) ","
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(72)
    }
    [29]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(73)
    }
    [30]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenInteger)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) "2"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(74)
    }
    [31]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenComma)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) ","
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(75)
    }
    [32]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) " "
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(76)
    }
    [33]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenVariable)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(7) "var_one"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(77)
    }
    [34]=>
    object(nicoSWD\Rule\TokenStream\Token\TokenClosingArray)#%d (2) {
      ["value":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      string(1) "]"
      ["offset":"nicoSWD\Rule\TokenStream\Token\BaseToken":private]=>
      int(84)
    }
  }
}
