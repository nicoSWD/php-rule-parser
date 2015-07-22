<?php

use nicoSWD\Rules;

require 'tests/bootstrap.php';

$highlighter = new Rules\Highlighter(new Rules\Tokenizer());

$ruleStr = '
/**
 * This is a test rule with comments
 */

// This is true
2 < 3 and (
    # this is false, because foo does not equal 4
    foo is 4
    # but bar is greater than 6
    or bar > 6
)';

echo $highlighter->highlightString($ruleStr);
