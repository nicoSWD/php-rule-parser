<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\Tokens\TokenArray;
use nicoSWD\Rules\Tokens\TokenBool;
use nicoSWD\Rules\Tokens\TokenFloat;
use nicoSWD\Rules\Tokens\TokenInteger;
use nicoSWD\Rules\Tokens\TokenNull;
use nicoSWD\Rules\Tokens\TokenString;

/**
 * Class NodeVariable
 * @package nicoSWD\Rules\AST
 */
final class NodeVariable extends BaseNode
{
    /**
     * @return \nicoSWD\Rules\Tokens\BaseToken
     */
    public function getNode()
    {
        $current = $this->ast->getStack()->current();
        $value = $this->ast->getVariable($current->getValue());

        switch (gettype($value)) {
            case 'string':
                $current = new TokenString('"' . $value . '"', $current->getOffset(), $current->getStack());
                break;
            case 'integer':
                $current = new TokenInteger($value, $current->getOffset(), $current->getStack());
                break;
            case 'boolean':
                $current = new TokenBool($value, $current->getOffset(), $current->getStack());
                break;
            case 'NULL':
                $current = new TokenNull($value, $current->getOffset(), $current->getStack());
                break;
            case 'double':
                $current = new TokenFloat($value, $current->getOffset(), $current->getStack());
                break;
            case 'array':
                $current = new TokenArray($value, $current->getOffset(), $current->getStack());
                break;
        }

        while ($current->supportsMethodCalls() && $this->hasMethodCall()) {
            $method = $this->getMethod($current);
            $current = $method->call($this->getFunctionArgs());
        }

        return $current;
    }
}
