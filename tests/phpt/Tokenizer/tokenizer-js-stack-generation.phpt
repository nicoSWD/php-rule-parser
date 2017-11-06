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
object(nicoSWD\Rule\Tokenizer\TokenStack)#%d (1) {
  ["storage":"SplObjectStorage":private]=>
  array(35) {
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenFunction)#%d (5) {
        ["value":protected]=>
        string(9) "parseInt("
        ["offset":protected]=>
        int(0)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#%d (5) {
        ["value":protected]=>
        string(3) ""2""
        ["offset":protected]=>
        int(9)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#%d (5) {
        ["value":protected]=>
        string(1) ")"
        ["offset":protected]=>
        int(12)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(13)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenEqual)#%d (5) {
        ["value":protected]=>
        string(2) "=="
        ["offset":protected]=>
        int(14)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(16)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenVariable)#%d (5) {
        ["value":protected]=>
        string(7) "var_two"
        ["offset":protected]=>
        int(17)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(24)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenAnd)#%d (5) {
        ["value":protected]=>
        string(2) "&&"
        ["offset":protected]=>
        int(25)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(27)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenOpeningParenthesis)#%d (5) {
        ["value":protected]=>
        string(1) "("
        ["offset":protected]=>
        int(28)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#%d (5) {
        ["value":protected]=>
        string(5) ""foo""
        ["offset":protected]=>
        int(29)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenMethod)#%d (5) {
        ["value":protected]=>
        string(13) ".toUpperCase("
        ["offset":protected]=>
        int(34)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#%d (5) {
        ["value":protected]=>
        string(1) ")"
        ["offset":protected]=>
        int(47)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(48)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenEqualStrict)#%d (5) {
        ["value":protected]=>
        string(3) "==="
        ["offset":protected]=>
        int(49)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(52)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#%d (5) {
        ["value":protected]=>
        string(5) ""FOO""
        ["offset":protected]=>
        int(53)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis)#%d (5) {
        ["value":protected]=>
        string(1) ")"
        ["offset":protected]=>
        int(58)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(59)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenOr)#%d (5) {
        ["value":protected]=>
        string(2) "||"
        ["offset":protected]=>
        int(60)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(62)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenInteger)#%d (5) {
        ["value":protected]=>
        string(1) "1"
        ["offset":protected]=>
        int(63)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(64)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenIn)#%d (5) {
        ["value":protected]=>
        string(2) "in"
        ["offset":protected]=>
        int(65)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(67)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenOpeningArray)#%d (5) {
        ["value":protected]=>
        string(1) "["
        ["offset":protected]=>
        int(68)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenEncapsedString)#%d (5) {
        ["value":protected]=>
        string(3) ""1""
        ["offset":protected]=>
        int(69)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenComma)#%d (5) {
        ["value":protected]=>
        string(1) ","
        ["offset":protected]=>
        int(72)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(73)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenInteger)#%d (5) {
        ["value":protected]=>
        string(1) "2"
        ["offset":protected]=>
        int(74)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenComma)#%d (5) {
        ["value":protected]=>
        string(1) ","
        ["offset":protected]=>
        int(75)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenSpace)#%d (5) {
        ["value":protected]=>
        string(1) " "
        ["offset":protected]=>
        int(76)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenVariable)#%d (5) {
        ["value":protected]=>
        string(7) "var_one"
        ["offset":protected]=>
        int(77)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
    ["%s"]=>
    array(2) {
      ["obj"]=>
      object(nicoSWD\Rule\TokenStream\Token\TokenClosingArray)#%d (5) {
        ["value":protected]=>
        string(1) "]"
        ["offset":protected]=>
        int(84)
        ["stack":protected]=>
        *RECURSION*
        ["position":protected]=>
        NULL
        ["line":protected]=>
        NULL
      }
      ["inf"]=>
      NULL
    }
  }
}
